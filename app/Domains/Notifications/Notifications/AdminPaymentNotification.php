<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Orders\Models\Order;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminPaymentNotification extends Notification
{
    use BroadcastsAsNotification;
    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => 'Payment Received',
            'message'     => "Order #{$this->order->order_number} — \${$this->order->final_amount} by {$this->order->user?->name}.",
            'type'        => 'payment',
            'link'        => '/admin/payments',
            'action_text' => 'View Payments',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'    => 'Payment Received',
            'body'     => "Payment for order #{$this->order->order_number} — \${$this->order->final_amount} by {$this->order->user?->name}.",
            'format'   => 'filament',
            'duration' => 'persistent',
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'View Payments',
                    'url'                  => '/admin/payments',
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='/admin/payments'",
                ],
            ],
        ];
    }
}
