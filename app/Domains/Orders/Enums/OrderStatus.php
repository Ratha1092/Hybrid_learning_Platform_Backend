<?php

namespace App\Domains\Orders\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}
