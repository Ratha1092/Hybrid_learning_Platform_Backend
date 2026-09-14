<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationLinks;
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
        $link = NotificationLinks::frontend("/instructor/finance/payouts/{$this->payoutRequestId}");

        return new BroadcastMessage([
            'title'       => 'Payout Rejected',
            'message'     => "Your payout request was rejected: {$this->reason}. Funds have been returned to your wallet.",
            'type'        => NotificationType::FINANCE->value,
            'link'        => $link,
            'action_text' => 'View Payout',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $link = NotificationLinks::frontend("/instructor/finance/payouts/{$this->payoutRequestId}");

        return [
            'title' => 'Payout Rejected',
            'message' => "Your payout request was rejected: {$this->reason}. Funds have been returned to your wallet.",
            'type' => NotificationType::FINANCE->value,
            'link' => $link,
            'action_text' => 'View Payout',
        ];
    }
}
