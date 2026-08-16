<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Orders\Models\Order;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminNewOrderNotification extends Notification
{
    use BroadcastsAsNotification;
    public function __construct(
        public readonly int    $orderId,
        public readonly string $customerName,
    ) {}

    public function via(object $notifiable): array
    {
        return \App\Domains\Notifications\Support\NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $order  = Order::find($this->orderId);
        $isFree = (float) ($order?->final_amount ?? 0) === 0.0;

        return new BroadcastMessage([
            'title'       => $isFree ? 'New Free Enrollment' : 'New Order Placed',
            'message'     => "Order #{$order?->order_number} by {$this->customerName}" . ($isFree ? ' (free).' : " — \${$order?->final_amount}."),
            'type'        => NotificationType::ORDER->value,
            'link'        => '/admin/orders',
            'action_text' => 'View Orders',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $order  = Order::find($this->orderId);
        $isFree = (float) ($order?->final_amount ?? 0) === 0.0;

        return [
            'type'     => NotificationType::ORDER->value,
            'title'    => $isFree ? 'New Free Enrollment' : 'New Order Placed',
            'message'  => "Order #{$order?->order_number} by {$this->customerName}" . ($isFree ? ' (free course).' : " — \${$order?->final_amount}."),
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
