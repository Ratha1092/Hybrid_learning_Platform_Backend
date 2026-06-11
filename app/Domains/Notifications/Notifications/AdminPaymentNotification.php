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
            'title'       => 'Payment Received',
            'message'     => "Payment completed for order #{$this->order->order_number} — \${$this->order->final_amount} by {$this->order->customer_name}.",
            'type'        => 'payment',
            'order_id'    => $this->order->id,
            'action_url'  => '/admin/payments',
            'action_text' => 'View Payments',
        ];
    }
}
