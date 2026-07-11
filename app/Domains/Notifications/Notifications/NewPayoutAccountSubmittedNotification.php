<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class NewPayoutAccountSubmittedNotification extends BaseNotification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly int    $payoutAccountId,
        public readonly string $userName,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'New Payout Account Submitted',
            'message'     => "{$this->userName} submitted a payout account for verification.",
            'type'        => NotificationType::FINANCE->value,
            'link'        => "/admin/payout-accounts/{$this->payoutAccountId}",
            'action_text' => 'Review Account',
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        $url = "/admin/payout-accounts/{$this->payoutAccountId}";

        return [
            'title'    => 'New Payout Account Submitted',
            'message'  => "{$this->userName} submitted a payout account for verification.",
            'format'   => 'filament',
            'duration' => 'persistent',
            'type'     => NotificationType::FINANCE->value,
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'Review Account',
                    'url'                  => $url,
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='{$url}'",
                ],
            ],
        ];
    }
}
