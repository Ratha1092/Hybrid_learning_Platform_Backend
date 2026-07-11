<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class PayoutRejectedNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly int    $payoutRequestId,
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Payout Rejected',
            'message'     => "Your payout request was rejected: {$this->reason}. Funds have been returned to your wallet.",
            'type'        => NotificationType::FINANCE->value,
            'link'        => "/instructor/finance/payouts/{$this->payoutRequestId}",
            'action_text' => 'View Payout',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payout Rejected',
            'message' => "Your payout request was rejected: {$this->reason}. Funds have been returned to your wallet.",
            'type' => NotificationType::FINANCE->value,
            'link' => "/instructor/finance/payouts/{$this->payoutRequestId}",
            'action_text' => 'View Payout',
        ];
    }
}
