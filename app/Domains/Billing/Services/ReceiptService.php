<?php

namespace App\Domains\Billing\Services;

use App\Domains\Billing\Mail\ReceiptIssuedMail;
use App\Domains\Billing\Models\Receipt;
use App\Domains\Orders\Models\Order;
use App\Support\ReportPdfExporter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    public function issue(Order $order): Receipt
    {
        return DB::transaction(function () use ($order) {
            $existing = Receipt::where('order_id', $order->id)->first();

            if ($existing) {
                return $existing;
            }

            $number = $this->nextNumber();

            $payment = $order->payment;
            $gateway = $payment?->payment_gateway instanceof \BackedEnum
                ? $payment->payment_gateway->value
                : ($payment?->payment_gateway ?? 'unknown');

            $receipt = Receipt::create([
                'order_id'        => $order->id,
                'receipt_number'  => $number,
                'amount'          => (float) $order->final_amount,
                'currency'        => $order->currency ?? 'USD',
                'payment_gateway' => $gateway,
                'paid_at'         => $order->paid_at ?? now(),
            ]);

            $order->load('items', 'user');
            $receipt->load('order.user');
            $this->storePdf($receipt);

            return $receipt->fresh();
        });
    }

    public function regeneratePdf(Receipt $receipt): void
    {
        $this->storePdf($receipt);
    }

    public function sendByEmail(Receipt $receipt): void
    {
        $receipt->load('order.user', 'order.items');
        $email = $receipt->order->user?->email ?? $receipt->order->customer_email;

        if (!$email) {
            return;
        }

        Mail::to($email)->queue(new ReceiptIssuedMail($receipt));
    }

    private function storePdf(Receipt $receipt): void
    {
        $receipt->load('order.items');

        $pdf  = ReportPdfExporter::render('billing.receipt', [
            'receipt' => $receipt,
            'order'   => $receipt->order,
            'items'   => $receipt->order->items,
        ]);

        $path = "receipts/{$receipt->receipt_number}.pdf";
        Storage::disk('r2-private')->put($path, $pdf);
        $receipt->updateQuietly(['pdf_path' => $path]);
    }

    private function nextNumber(): string
    {
        $seq = DB::table('document_sequences')
            ->where('type', 'receipt')
            ->lockForUpdate()
            ->first();

        $next = $seq->last_number + 1;

        DB::table('document_sequences')
            ->where('type', 'receipt')
            ->update(['last_number' => $next]);

        return sprintf('RCP-%s-%06d', now()->year, $next);
    }
}
