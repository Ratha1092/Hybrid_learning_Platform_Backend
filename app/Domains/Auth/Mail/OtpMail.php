<?php

namespace App\Domains\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $email,
        public readonly string $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Verification Code — ' . config('app.name'), tags: ['critical']);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.auth.otp');
    }

    public function attachments(): array
    {
        return [];
    }
}
