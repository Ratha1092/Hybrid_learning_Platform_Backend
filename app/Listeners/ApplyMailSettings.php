<?php

namespace App\Listeners;

use App\Domains\System\Models\Setting;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;

/**
 * Single choke point for all outgoing mail, wiring in several admin Settings
 * that would otherwise require editing every Mailable/call site individually:
 *
 *  - smtp_test_mode: suppress real delivery, log instead ("test sink").
 *  - email_notifications_enabled: master off-switch — skipped for mail
 *    tagged 'critical' (OTP/2FA/email-verification) so it can never silently
 *    lock a user out of their account.
 *  - mail_from_name / mail_from_address / reply_to_email: override the
 *    sender identity on every outgoing message.
 */
class ApplyMailSettings
{
    public function handle(MessageSending $event): bool
    {
        if (Setting::get('smtp_test_mode', false)) {
            Log::info('smtp_test_mode: outgoing email suppressed', [
                'to' => $this->addressList($event, 'getTo'),
                'subject' => $event->message->getSubject(),
            ]);

            return false;
        }

        $isCritical = collect($event->message->getHeaders()->all('X-Tag'))
            ->contains(fn ($header) => $header->getBodyAsString() === 'critical');

        if (!$isCritical && !Setting::get('email_notifications_enabled', true)) {
            return false;
        }

        $fromName = Setting::get('mail_from_name');
        $fromAddress = Setting::get('mail_from_address');
        if ($fromAddress) {
            $event->message->from(new Address($fromAddress, (string) ($fromName ?: '')));
        }

        $replyTo = Setting::get('reply_to_email');
        if ($replyTo) {
            $event->message->replyTo($replyTo);
        }

        return true;
    }

    private function addressList(MessageSending $event, string $method): string
    {
        return collect($event->message->{$method}())
            ->map(fn ($address) => $address->getAddress())
            ->implode(', ');
    }
}
