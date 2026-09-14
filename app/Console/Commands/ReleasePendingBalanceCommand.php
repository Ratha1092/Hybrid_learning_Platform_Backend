<?php

namespace App\Console\Commands;

use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\System\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleasePendingBalanceCommand extends Command
{
    protected $signature   = 'wallet:release-pending-balance';
    protected $description = 'Move earnings past the payout hold period from pending_balance into the withdrawable balance';

    public function handle(): int
    {
        $holdDays = (int) Setting::get('payout_hold_period_days', 14);

        $instructorIds = RevenueShare::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays($holdDays))
            ->distinct()
            ->pluck('instructor_id');

        $released = 0;

        foreach ($instructorIds as $instructorId) {
            try {
                $amount = DB::transaction(function () use ($instructorId, $holdDays) {
                    $wallet = InstructorWallet::where('instructor_id', $instructorId)
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet) {
                        return null;
                    }

                    // Re-select and lock under the transaction so a concurrent run
                    // (or manual admin action) can't have already distributed these
                    // rows between the outer query above and this update.
                    $shares = RevenueShare::where('instructor_id', $instructorId)
                        ->where('status', 'pending')
                        ->where('created_at', '<=', now()->subDays($holdDays))
                        ->lockForUpdate()
                        ->get();

                    if ($shares->isEmpty()) {
                        return null;
                    }

                    $amount = $shares->sum('instructor_amount');

                    // A release can only move funds that are actually held in
                    // the wallet. Without this check a missing earlier wallet
                    // credit turns a release into a positive available balance
                    // and a negative pending balance.
                    if ((float) $wallet->pending_balance + 0.00001 < (float) $amount) {
                        throw new \LogicException(sprintf(
                            'Wallet pending balance (%s) is less than the eligible release amount (%s).',
                            $wallet->pending_balance,
                            $amount
                        ));
                    }

                    $wallet->decrement('pending_balance', $amount);
                    $wallet->increment('balance', $amount);

                    RevenueShare::whereIn('id', $shares->pluck('id'))->update(['status' => 'distributed']);

                    return $amount;
                });

                if ($amount === null) {
                    continue;
                }

                ++$released;
                $this->line(sprintf('  instructor #%d  released  %s', $instructorId, $amount));
            } catch (\Throwable $e) {
                Log::error('Pending balance release failed', [
                    'instructor_id' => $instructorId,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  instructor #{$instructorId}: {$e->getMessage()}");
            }
        }

        $this->info("Released pending balance for {$released} instructor(s).");

        return self::SUCCESS;
    }
}
