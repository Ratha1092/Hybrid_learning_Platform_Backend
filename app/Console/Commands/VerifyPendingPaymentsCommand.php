<?php

namespace App\Console\Commands;

use App\Domains\Payments\Models\Payment;
use App\Domains\Payments\Services\BakongKhqrService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifyPendingPaymentsCommand extends Command
{
    protected $signature = 'payments:verify-pending';
    protected $description = 'Verify all pending Bakong payments against the gateway';

    public function handle(BakongKhqrService $service): void
    {
        $payments = Payment::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('payment_gateway', 'bakong')
            ->where('expires_at', '>', now())
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No pending payments to verify.');
            return;
        }

        $this->info("Verifying {$payments->count()} pending payment(s)...");

        foreach ($payments as $payment) {
            try {
                $updated = $service->verifyPayment($payment);
                $this->line("  Payment #{$payment->id}: {$updated->status->value}");
            } catch (\Throwable $e) {
                Log::error('Auto-verification failed for payment', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  Payment #{$payment->id}: failed — {$e->getMessage()}");
            }
        }
    }
}
