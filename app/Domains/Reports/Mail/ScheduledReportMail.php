<?php

namespace App\Domains\Reports\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $reportLabel,
        public string $periodSummary,
        public string $attachmentContent,
        public string $attachmentFilename,
        public string $mimeType,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->reportLabel} Report — {$this->periodSummary}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reports.scheduled-report',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->attachmentContent, $this->attachmentFilename)
                ->withMime($this->mimeType),
        ];
    }
}
