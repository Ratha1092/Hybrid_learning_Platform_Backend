<?php

namespace App\Domains\Users\Mail;

use App\Domains\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstructorApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Instructor Application is Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.instructor.approved',
        );
    }
}
