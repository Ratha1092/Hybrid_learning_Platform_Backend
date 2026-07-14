<?php

namespace App\Domains\Billing\Services;

use App\Domains\Billing\Mail\InvoiceIssuedMail;
use App\Domains\Billing\Models\Invoice;
use App\Domains\Billing\Models\InvoiceItem;
use App\Domains\Orders\Models\Order;
use App\Domains\System\Models\Setting;
use App\Support\ReportPdfExporter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function issue(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            $existing = Invoice::where('order_id', $order->id)
                ->where('type', Invoice::TYPE_INVOICE)
                ->first();

            if ($existing) {
                return $existing;
            }

            $number = $this->nextNumber('invoice', 'INV');

            $order->updateQuietly(['invoice_number' => $number]);

            $taxRate   = (float) Setting::get('tax_percentage', 0);
            $subtotal  = (float) $order->total_amount;
            $discount  = (float) $order->discount_amount;
            $taxAmount = round($subtotal * $taxRate / 100, 2);
            $total     = (float) $order->final_amount;

            $invoice = Invoice::create([
                'order_id'       => $order->id,
                'type'           => Invoice::TYPE_INVOICE,
                'invoice_number' => $number,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax_rate'       => $taxRate,
                'tax_amount'     => $taxAmount,
                'total'          => $total,
                'currency'       => $order->currency ?? 'USD',
                'status'         => Invoice::STATUS_ISSUED,
                'issued_at'      => now(),
            ]);

            $order->load('items');
            foreach ($order->items as $item) {
                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'course_title' => $item->course_title,
                    'unit_price'   => (float) $item->price,
                    'discount'     => (float) $item->discount_amount,
                    'amount'       => (float) $item->final_amount,
                ]);
            }

            $invoice->load('items', 'order.user');
            $this->storePdf($invoice);

            return $invoice->fresh();
        });
    }

    public function issueCreditNote(Order $order, string $reason): Invoice
    {
        return DB::transaction(function () use ($order, $reason) {
            $original = Invoice::where('order_id', $order->id)
                ->where('type', Invoice::TYPE_INVOICE)
                ->firstOrFail();

            $number = $this->nextNumber('credit_note', 'CN');

            $creditNote = Invoice::create([
                'order_id'            => $order->id,
                'type'                => Invoice::TYPE_CREDIT_NOTE,
                'original_invoice_id' => $original->id,
                'invoice_number'      => $number,
                'subtotal'            => -$original->subtotal,
                'discount'            => -$original->discount,
                'tax_rate'            => $original->tax_rate,
                'tax_amount'          => -$original->tax_amount,
                'total'               => -$original->total,
                'currency'            => $original->currency,
                'status'              => Invoice::STATUS_ISSUED,
                'issued_at'           => now(),
            ]);

            foreach ($original->items as $item) {
                InvoiceItem::create([
                    'invoice_id'   => $creditNote->id,
                    'course_title' => $item->course_title,
                    'unit_price'   => -$item->unit_price,
                    'discount'     => -$item->discount,
                    'amount'       => -$item->amount,
                ]);
            }

            $creditNote->load('items', 'order.user', 'originalInvoice');
            $this->storePdf($creditNote, $reason);

            return $creditNote->fresh();
        });
    }

    public function regeneratePdf(Invoice $invoice): void
    {
        $invoice->load('items', 'order.user', 'billingAddress', 'originalInvoice');
        $this->storePdf($invoice);
    }

    public function sendByEmail(Invoice $invoice): void
    {
        $invoice->load('order.user');
        $email = $invoice->order->user?->email ?? $invoice->order->customer_email;

        if (!$email) {
            return;
        }

        Mail::to($email)->queue(new InvoiceIssuedMail($invoice));
    }

    private function storePdf(Invoice $invoice, string $reason = ''): void
    {
        $view = $invoice->isCreditNote() ? 'billing.credit-note' : 'billing.invoice';

        $pdf  = ReportPdfExporter::render($view, [
            'invoice' => $invoice,
            'items'   => $invoice->items,
            'reason'  => $reason,
        ]);

        $path = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('r2-private')->put($path, $pdf);
        $invoice->updateQuietly(['pdf_path' => $path]);
    }

    private function nextNumber(string $type, string $prefix): string
    {
        $seq = DB::table('document_sequences')
            ->where('type', $type)
            ->lockForUpdate()
            ->first();

        $next = $seq->last_number + 1;

        DB::table('document_sequences')
            ->where('type', $type)
            ->update(['last_number' => $next]);

        return sprintf('%s-%s-%06d', $prefix, now()->year, $next);
    }
}
