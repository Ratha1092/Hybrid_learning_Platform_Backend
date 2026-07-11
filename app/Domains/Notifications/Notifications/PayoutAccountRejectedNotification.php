<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class PayoutAccountRejectedNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Payout Account Rejected',
            'message'     => "Your payout account was rejected: {$this->reason}",
            'type'        => NotificationType::FINANCE->value,
            'link'        => '/instructor/finance/payout-account',
            'action_text' => 'Update Payout Account',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payout Account Rejected',
            'message' => "Your payout account was rejected: {$this->reason}",
            'type' => NotificationType::FINANCE->value,
            'link' => '/instructor/finance/payout-account',
            'action_text' => 'Update Payout Account',
        ];
    }
}
