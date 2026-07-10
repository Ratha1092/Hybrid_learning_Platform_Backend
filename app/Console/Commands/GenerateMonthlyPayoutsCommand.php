<?php

namespace App\Console\Commands;

use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Services\PayoutService;
use App\Domains\Notifications\Notifications\MonthlyPayoutGeneratedNotification;
use App\Domains\System\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyPayoutsCommand extends Command
{
    protected $signature   = 'payouts:generate-monthly';
    protected $description = 'Auto-generate a payout request for every instructor with a verified payout account and a balance above the minimum payout threshold';

    public function handle(PayoutService $payoutService): int
    {
        $minimum = (float) Setting::get('minimum_payout_amount', 50);
        $notify  = (bool) Setting::get('payout_notification', true);

        $wallets = InstructorWallet::where('balance', '>=', $minimum)
            ->whereHas('instructor.payoutAccount', fn ($q) => $q->where('status', 'verified'))
            ->with('instructor')
            ->get();

        $generated = 0;

        foreach ($wallets as $wallet) {
            try {
                $payout = $payoutService->requestPayout($wallet->instructor, (float) $wallet->balance, 'monthly_auto');
                ++$generated;

                $this->line(sprintf('  #%d  requested  %s %s', $wallet->instructor_id, $payout->amount, $payout->currency));

                if ($notify) {
                    $wallet->instructor->notify(new MonthlyPayoutGeneratedNotification(
                        $payout->id,
                        (float) $payout->amount,
                        $payout->currency
                    ));
                }
            } catch (\Throwable $e) {
                Log::error('Monthly payout generation failed', [
                    'instructor_id' => $wallet->instructor_id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  #{$wallet->instructor_id}: {$e->getMessage()}");
            }
        }

        $this->info("Generated {$generated} monthly payout request(s).");

        return self::SUCCESS;
    }
}
