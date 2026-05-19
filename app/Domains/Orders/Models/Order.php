<?php

namespace App\Domains\Orders\Models;

use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'order_number',
        'user_id',
        'total_amount',
        'discount_amount',
        'final_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'customer_name',
        'customer_email',
        'paid_at',
        'cancelled_at',
        'refunded_at',
        'coupon_code',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'payment_status' => OrderPaymentStatus::class,
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)
            ->latestOfMany();
    }
    public function enrollments(): HasMany
    {
        return $this->hasMany(
            \App\Domains\Learning\Models\Enrollment::class
        );
    }
    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::Cancelled;
    }

    public function isRefunded(): bool
    {
        return $this->status === OrderStatus::Refunded;
    }
    public function isPaid(): bool
    {
        return $this->payment_status === OrderPaymentStatus::Paid;
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === OrderPaymentStatus::Pending;
    }

    public function isPaymentFailed(): bool
    {
        return $this->payment_status === OrderPaymentStatus::Failed;
    }
}