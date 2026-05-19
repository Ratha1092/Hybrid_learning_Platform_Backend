<?php

namespace App\Domains\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'course_id',
        'instructor_id',
        'course_title',
        'price',
        'discount_amount',
        'final_amount',
        'commission_percentage',
        'instructor_amount',
        'platform_amount',
        'is_refunded',
        'refunded_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'instructor_amount' => 'decimal:2',
        'platform_amount' => 'decimal:2',
        'is_refunded' => 'boolean',
        'refunded_at' => 'datetime',
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(\App\Domains\Users\Models\User::class,'instructor_id'
        );
    }
}