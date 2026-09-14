<?php

namespace App\Console\Commands;

use App\Domains\Billing\Services\InvoiceService;
use App\Domains\Billing\Services\ReceiptService;
use App\Domains\Orders\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackfillBillingDocumentsCommand extends Command
{
    protected $signature = 'billing:backfill-documents {--dry-run : List affected orders without issuing anything}';
    protected $description = 'Issue Invoice/Receipt records for paid orders that predate the invoice()/receipt() wiring (or whose queued issuance never ran)';

    public function handle(InvoiceService $invoiceService, ReceiptService $receiptService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $orders = Order::query()
            ->where('payment_status', 'paid')
            ->where(fn ($q) => $q->whereDoesntHave('invoice')->orWhereDoesntHave('receipt'))
            ->with('items', 'payment', 'user', 'invoice', 'receipt')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No paid orders are missing an invoice or receipt.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("{$orders->count()} paid order(s) would be backfilled:");
            foreach ($orders as $order) {
                $this->line(sprintf(
                    '  #%d  %s  invoice=%s  receipt=%s',
                    $order->id,
                    $order->order_number,
                    $order->invoice ? 'have' : 'missing',
                    $order->receipt ? 'have' : 'missing',
                ));
            }
            return self::SUCCESS;
        }

        $this->info("Backfilling documents for {$orders->count()} paid order(s)...");

        $issued = $failed = 0;

        foreach ($orders as $order) {
            try {
                $receiptService->issue($order); // idempotent — no-ops if one already exists
                $invoiceService->issue($order);
                ++$issued;
                $this->line("  #{$order->id}  {$order->order_number}  done");
            } catch (\Throwable $e) {
                ++$failed;
                Log::error('Billing document backfill failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  #{$order->id}  {$order->order_number}  FAILED: {$e->getMessage()}");
            }
        }

        $this->info("Done — issued: {$issued}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
