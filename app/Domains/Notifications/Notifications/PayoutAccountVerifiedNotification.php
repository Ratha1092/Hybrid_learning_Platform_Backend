<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class PayoutAccountVerifiedNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $link = NotificationLinks::frontend('/instructor/finance/payout-account');

        return new BroadcastMessage([
            'title'       => 'Payout Account Verified',
            'message'     => 'Your payout account has been verified. You can now request payouts.',
            'type'        => NotificationType::FINANCE->value,
            'link'        => $link,
            'action_text' => 'View Payout Account',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $link = NotificationLinks::frontend('/instructor/finance/payout-account');

        return [
            'title' => 'Payout Account Verified',
            'message' => 'Your payout account has been verified. You can now request payouts.',
            'type' => NotificationType::FINANCE->value,
            'link' => $link,
            'action_text' => 'View Payout Account',
        ];
    }
}
