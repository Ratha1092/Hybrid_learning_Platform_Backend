<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Orders\Models\Order;
use App\Filament\Resources\Payments\PaymentResource;
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
        $paymentUrl = PaymentResource::getUrl('view', ['record' => $this->order->payment?->id]);

        return new BroadcastMessage([
            'title'       => 'Payment Received',
            'message'     => "Order #{$this->order->order_number} — \${$this->order->final_amount} by {$this->order->user?->name}.",
            'type'        => NotificationType::PAYMENT->value,
            'link'        => $paymentUrl,
            'action_text' => 'View Payment',
        ]);
    }

    public function toArray(object $notifiable): array
    {
        $paymentUrl = PaymentResource::getUrl('view', ['record' => $this->order->payment?->id]);

        return [
            'title'    => 'Payment Received',
            'message'  => "Payment for order #{$this->order->order_number} — \${$this->order->final_amount} by {$this->order->user?->name}.",
            'type'     => NotificationType::PAYMENT->value,
            'format'   => 'filament',
            'duration' => 'persistent',
            'actions'  => [
                [
                    'name'                 => 'view',
                    'label'                => 'View Payment',
                    'url'                  => $paymentUrl,
                    'view'                 => 'filament-actions::link-action',
                    'shouldOpenUrlInNewTab' => false,
                    'alpineClickHandler'   => "window.location.href='{$paymentUrl}'",
                ],
            ],
        ];
    }
}
