<?php

namespace App\Domains\Billing\Listeners;

use App\Domains\Billing\Services\InvoiceService;
use App\Domains\Billing\Services\ReceiptService;
use App\Domains\Payments\Events\PaymentSuccessEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class IssueDocumentsOnPayment implements ShouldQueue
{
    public string $queue = 'default';
    public int $tries = 3;

    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly ReceiptService $receiptService,
    ) {}

    public function tags(): array
    {
        return ['billing', 'documents'];
    }

    public function handle(PaymentSuccessEvent $event): void
    {
        $order = $event->order->load('items', 'payment', 'user');

        try {
            $receipt = $this->receiptService->issue($order);
            $this->receiptService->sendByEmail($receipt);
        } catch (\Throwable $e) {
            Log::error('Failed to issue receipt', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        try {
            $invoice = $this->invoiceService->issue($order);
            $this->invoiceService->sendByEmail($invoice);
        } catch (\Throwable $e) {
            Log::error('Failed to issue invoice', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
