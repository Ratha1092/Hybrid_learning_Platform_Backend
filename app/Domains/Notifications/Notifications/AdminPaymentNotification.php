<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Orders\Models\Order;
use Illuminate\Notifications\Notification;

class AdminPaymentNotification extends Notification
{
    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
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
