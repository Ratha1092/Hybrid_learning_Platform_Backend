<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class PayoutAccountVerifiedNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Payout Account Verified',
            'message'     => 'Your payout account has been verified. You can now request payouts.',
            'type'        => NotificationType::FINANCE->value,
            'link'        => '/instructor/finance/payout-account',
            'action_text' => 'View Payout Account',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payout Account Verified',
            'message' => 'Your payout account has been verified. You can now request payouts.',
            'type' => NotificationType::FINANCE->value,
            'link' => '/instructor/finance/payout-account',
            'action_text' => 'View Payout Account',
        ];
    }
}
