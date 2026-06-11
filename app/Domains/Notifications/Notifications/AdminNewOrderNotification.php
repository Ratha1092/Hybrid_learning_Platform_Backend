<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Orders\Models\Order;
use Illuminate\Notifications\Notification;

class AdminNewOrderNotification extends Notification
{
    public function __construct(
        public readonly Order $order,
        public readonly string $customerName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isFree = (float) $this->order->final_amount === 0.0;

        return [
            'title'       => $isFree ? 'New Free Enrollment' : 'New Order Placed',
            'message'     => "Order #{$this->order->order_number} by {$this->customerName}" . ($isFree ? ' (free course).' : " — \${$this->order->final_amount}."),
            'type'        => 'order',
            'order_id'    => $this->order->id,
            'action_url'  => '/admin/orders',
            'action_text' => 'View Orders',
        ];
    }
}
