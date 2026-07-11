<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class MonthlyPayoutGeneratedNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly int    $payoutRequestId,
        public readonly float  $amount,
        public readonly string $currency,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Monthly Payout Requested',
            'message'     => "Your end-of-month payout of {$this->amount} {$this->currency} has been requested and is awaiting approval.",
            'type'        => NotificationType::FINANCE->value,
            'link'        => "/instructor/finance/payouts/{$this->payoutRequestId}",
            'action_text' => 'View Payout',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Monthly Payout Requested',
            'message' => "Your end-of-month payout of {$this->amount} {$this->currency} has been requested and is awaiting approval.",
            'type' => NotificationType::FINANCE->value,
            'link' => "/instructor/finance/payouts/{$this->payoutRequestId}",
            'action_text' => 'View Payout',
        ];
    }
}
