<?php

namespace App\Domains\Payments\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Domains\Orders\Models\Order;

class PaymentSuccessEvent
{
    use Dispatchable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        // preload relations to avoid lazy loading in queue
        $this->order = $order->load('items');
    }
}