<?php

namespace App\Domains\Finance\Services;

use App\Domains\Finance\Mail\PayoutReceiptIssuedMail;
use App\Domains\Finance\Models\PayoutReceipt;
use App\Domains\Finance\Models\PayoutRequest;
use App\Support\ReportPdfExporter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PayoutReceiptService
{
    public function issue(PayoutRequest $payout): PayoutReceipt
    {
        return DB::transaction(function () use ($payout) {
            $existing = PayoutReceipt::where('payout_request_id', $payout->id)->first();
            if ($existing) {
                return $existing;
            }

            $receipt = PayoutReceipt::create([
                'payout_request_id' => $payout->id,
                'receipt_number' => $this->nextNumber(),
                'amount' => (float) $payout->amount,
                'currency' => $payout->currency ?? 'USD',
                'payment_method' => $payout->payment_method,
                'paid_at' => $payout->processed_at ?? now(),
            ]);

            $receipt->load('payoutRequest.instructor', 'payoutRequest.payoutAccount');
            $this->storePdf($receipt);

            return $receipt->fresh();
        });
    }

    public function regeneratePdf(PayoutReceipt $receipt): void
    {
        $this->storePdf($receipt);
    }

    public function sendByEmail(PayoutReceipt $receipt): void
    {
        $receipt->load('payoutRequest.instructor');
        $email = $receipt->payoutRequest->instructor?->email;

        if (!$email) {
            return;
        }

        Mail::to($email)->queue(new PayoutReceiptIssuedMail($receipt));
    }

    private function storePdf(PayoutReceipt $receipt): void
    {
        $receipt->load('payoutRequest.instructor', 'payoutRequest.payoutAccount');

        $pdf = ReportPdfExporter::render('finance.payout-receipt', [
            'receipt' => $receipt,
            'payout' => $receipt->payoutRequest,
        ]);

        $path = "payout-receipts/{$receipt->receipt_number}.pdf";
        Storage::disk('local')->put($path, $pdf);
        $receipt->updateQuietly(['pdf_path' => $path]);
    }

    private function nextNumber(): string
    {
        $seq = DB::table('document_sequences')->where('type', 'payout_receipt')->lockForUpdate()->first();
        $next = $seq->last_number + 1;
        DB::table('document_sequences')->where('type', 'payout_receipt')->update(['last_number' => $next]);

        return sprintf('PYT-%s-%06d', now()->year, $next);
    }
}
