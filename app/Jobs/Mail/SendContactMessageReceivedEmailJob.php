<?php

namespace App\Jobs\Mail;

use App\Domains\System\Models\ContactMessage;
use App\Domains\System\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Resend\Laravel\Facades\Resend;

class SendContactMessageReceivedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly int $contactMessageId,
    ) {}

    public function tags(): array
    {
        return ['mail', 'contact-message', "contact-message:{$this->contactMessageId}"];
    }

    public function middleware(): array
    {
        return [new RateLimited('resend-emails')];
    }

    public function handle(): void
    {
        $contactMessage = ContactMessage::find($this->contactMessageId);
        if (! $contactMessage) {
            return;
        }

        $supportEmail = Setting::get('support_email');
        if (! $supportEmail) {
            return;
        }

        Resend::emails()->send([
            'from'     => config('mail.from.name') . ' <' . config('mail.from.address') . '>',
            'to'       => [$supportEmail],
            'reply_to' => [$contactMessage->email],
            'subject'  => 'New Contact Message: ' . $contactMessage->subject,
            'html'     => view('emails.system.contact-message-received', [
                'contactMessage' => $contactMessage,
                'adminUrl'       => route('filament.admin.pages.contact-messages'),
            ])->render(),
        ]);
    }
}
