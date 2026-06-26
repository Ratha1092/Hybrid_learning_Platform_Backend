<?php

namespace App\Domains\Billing\Mail;

use App\Domains\Billing\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ReceiptIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Receipt $receipt) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Payment Receipt {$this->receipt->receipt_number}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.billing.receipt-issued');
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
