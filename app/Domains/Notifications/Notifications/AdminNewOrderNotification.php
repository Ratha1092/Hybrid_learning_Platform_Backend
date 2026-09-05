<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Orders\Models\Order;
use App\Filament\Resources\Orders\OrderResource;
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
        $orderUrl = OrderResource::getUrl('view', ['record' => $this->orderId]);

        return new BroadcastMessage([
            'title'       => $isFree ? 'New Free Enrollment' : 'New Order Placed',
            'message'     => "Order #{$order?->order_number} by {$this->customerName}" . ($isFree ? ' (free).' : " — \${$order?->final_amount}."),
            'type'        => NotificationType::ORDER->value,
            'link'        => $orderUrl,
            'action_text' => 'View Order',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $order  = Order::find($this->orderId);
        $isFree = (float) ($order?->final_amount ?? 0) === 0.0;
        $orderUrl = OrderResource::getUrl('view', ['record' => $this->orderId]);

        return [
            'type'     => NotificationType::ORDER->value,
            'title'    => $isFree ? 'New Free Enrollment' : 'New Order Placed',
            'message'  => "Order #{$order?->order_number} by {$this->customerName}" . ($isFree ? ' (free course).' : " — \${$order?->final_amount}."),
            'format'   => 'filament',
            'duration' => 'persistent',
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'View Order',
                    'url'                  => $orderUrl,
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='{$orderUrl}'",
                ],
            ],
        ];
    }
}
