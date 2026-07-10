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

        $shares = RevenueShare::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays($holdDays))
            ->get()
            ->groupBy('instructor_id');

        $released = 0;

        foreach ($shares as $instructorId => $instructorShares) {
            $amount = $instructorShares->sum('instructor_amount');
            $ids    = $instructorShares->pluck('id');

            try {
                DB::transaction(function () use ($instructorId, $amount, $ids) {
                    $wallet = InstructorWallet::where('instructor_id', $instructorId)
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet) {
                        return;
                    }

                    $wallet->decrement('pending_balance', $amount);
                    $wallet->increment('balance', $amount);

                    RevenueShare::whereIn('id', $ids)->update(['status' => 'distributed']);
                });

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
