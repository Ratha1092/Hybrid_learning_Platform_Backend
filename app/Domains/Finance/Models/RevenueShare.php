<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueShare extends Model
{
    protected $fillable = [
        'order_item_id',
        'instructor_id',
        'total_amount',
        'platform_amount',
        'instructor_amount',
        'commission_percentage',
        'status',
    ];

    public function orderItem()
    {
        return $this->belongsTo(\App\Domains\Orders\Models\OrderItem::class);
    }
}