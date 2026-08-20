<?php

namespace App\Jobs\Mail;

use App\Domains\System\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Resend\Laravel\Facades\Resend;

class SendContactMessageReplyEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly int $contactMessageId,
    ) {}

    public function tags(): array
    {
        return ['mail', 'contact-reply', "contact-message:{$this->contactMessageId}"];
    }

    public function middleware(): array
    {
        return [new RateLimited('resend-emails')];
    }

    public function handle(): void
    {
        $contactMessage = ContactMessage::find($this->contactMessageId);
        if (! $contactMessage || ! $contactMessage->reply_message) {
            return;
        }

        Resend::emails()->send([
            'from'    => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'      => [$contactMessage->email],
            'subject' => 'Re: ' . $contactMessage->subject,
            'html'    => view('emails.system.contact-message-reply', [
                'contactMessage' => $contactMessage,
            ])->render(),
        ]);
    }
}
