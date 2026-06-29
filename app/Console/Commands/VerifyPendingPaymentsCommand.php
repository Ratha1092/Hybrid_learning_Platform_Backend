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
        // Pass 1: expire all payments whose QR TTL has passed without calling Bakong.
        // These need no API call — the window is closed, just write the status.
        $stale = Payment::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('payment_gateway', 'bakong')
            ->where('expires_at', '<', now())
            ->get();

        $expired = 0;
        foreach ($stale as $payment) {
            try {
                $service->expirePayment($payment);
                ++$expired;
                $this->line(sprintf('  #%d  expired  (TTL passed)', $payment->id));
            } catch (\Throwable $e) {
                Log::error('Auto-expire failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
                $this->error("  #{$payment->id}: {$e->getMessage()}");
            }
        }

        // Pass 2: verify payments still within their TTL window via the Bakong API.
        $payments = Payment::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('payment_gateway', 'bakong')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->orderBy('last_verified_at', 'asc')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($payments->isEmpty() && $stale->isEmpty()) {
            $this->info('No payments to process.');
            return self::SUCCESS;
        }

        if ($payments->isNotEmpty()) {
            $this->info("Verifying {$payments->count()} live payment(s) via Bakong...");
        }

        $paid = $failed = $pending = 0;

        foreach ($payments as $payment) {
            try {
                $result = $service->forceVerifyPayment($payment);

                match (true) {
                    $result->isPaid()   => ++$paid,
                    $result->isFailed() => ++$failed,
                    default             => ++$pending,
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

        $this->info("Done — expired: {$expired}, paid: {$paid}, still pending: {$pending}, failed: {$failed}");

        return self::SUCCESS;
    }
}
