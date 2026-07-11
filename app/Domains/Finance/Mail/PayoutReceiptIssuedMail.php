<?php

namespace App\Domains\Finance\Mail;

use App\Domains\Finance\Models\PayoutReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PayoutReceiptIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly PayoutReceipt $receipt) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Payout Receipt {$this->receipt->receipt_number}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.finance.payout-receipt-issued');
    }

    public function attachments(): array
    {
        if (!$this->receipt->pdf_path || !Storage::disk('local')->exists($this->receipt->pdf_path)) {
            return [];
        }

        return [
            Attachment::fromData(
                fn () => Storage::disk('local')->get($this->receipt->pdf_path),
                $this->receipt->receipt_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
