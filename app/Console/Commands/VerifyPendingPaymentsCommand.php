<?php

namespace App\Console\Commands;

use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\BakongKhqrService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifyPendingPaymentsCommand extends Command
{
    protected $signature   = 'payments:verify-pending {--limit=20 : Max payments to verify per run}';
    protected $description = 'Auto force-verify all pending/processing Bakong payments';

    public function handle(BakongKhqrService $service): int
    {
        $payments = Payment::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('payment_gateway', 'bakong')
            ->orderBy('last_verified_at', 'asc')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No payments to verify.');
            return self::SUCCESS;
        }

        $this->info("Verifying {$payments->count()} payment(s)...");

        $paid = $failed = $pending = 0;

        foreach ($payments as $payment) {
            try {
                $result = $service->forceVerifyPayment($payment);

                match (true) {
                    $result->isPaid() => ++$paid,
                    $result->isFailed() => ++$failed,
                    default => ++$pending,
                };

                $this->line(sprintf(
                    '  #%d  %-12s  attempts: %d',
                    $result->id,
                    $result->status->value,
                    $result->verification_attempts
                ));

            } catch (\Throwable $e) {
                ++$failed;
                Log::error('Auto-verify failed', [
                    'payment_id' => $payment->id,
                    'error'      => $e->getMessage(),
                ]);
                $this->error("  #{$payment->id}: {$e->getMessage()}");
            }

            // Small delay to avoid hammering the Bakong API
            usleep(300_000); // 300ms between each call
        }

        $this->info("Done — paid: {$paid}, still pending: {$pending}, failed: {$failed}");

        return self::SUCCESS;
    }
}
