<?php

namespace App\Domains\Auth\Mail;

use App\Domains\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $verificationLink,
        public readonly string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify Your Email Address');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.auth.verify-email');
    }

    public function attachments(): array
    {
        return [];
    }
}
