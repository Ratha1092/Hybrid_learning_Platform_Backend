<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Orders\Models\Order;
use Illuminate\Notifications\Notification;

class AdminNewOrderNotification extends Notification
{
    public function __construct(
        public readonly int    $orderId,
        public readonly string $customerName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $order  = Order::find($this->orderId);
        $isFree = (float) ($order?->final_amount ?? 0) === 0.0;

        return [
            'title'    => $isFree ? 'New Free Enrollment' : 'New Order Placed',
            'body'     => "Order #{$order?->order_number} by {$this->customerName}" . ($isFree ? ' (free course).' : " — \${$order?->final_amount}."),
            'format'   => 'filament',
            'duration' => 'persistent',
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'View Orders',
                    'url'                  => '/admin/orders',
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='/admin/orders'",
                ],
            ],
        ];
    }
}
