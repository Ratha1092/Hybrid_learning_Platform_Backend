<?php

namespace App\Domains\Orders\Models;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Billing\Models\Receipt;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Promotions\Models\Coupon;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use SoftDeletes;

    // Only computed/exposed when the invoice/receipt relations are eager
    // loaded (see the accessors below) — not present on every Order fetch.
    protected $appends = [
        'invoice_id',
        'invoice_number',
        'receipt_id',
        'receipt_number',
    ];

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
        'coupon_code',
        'coupon_id',
        'deleted_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'payment_status' => OrderPaymentStatus::class,
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Order $order) {
            if (auth()->check()) {
                $order->deleted_by = auth()->id();
                $order->saveQuietly();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
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

    // Excludes credit notes, which also belongTo an Order via the same
    // order_id — this is specifically "the" invoice document for the order.
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->where('type', Invoice::TYPE_INVOICE);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function getInvoiceIdAttribute(): ?int
    {
        return $this->relationLoaded('invoice') ? $this->invoice?->id : null;
    }

    public function getInvoiceNumberAttribute(): ?string
    {
        return $this->relationLoaded('invoice') ? $this->invoice?->invoice_number : null;
    }

    public function getReceiptIdAttribute(): ?int
    {
        return $this->relationLoaded('receipt') ? $this->receipt?->id : null;
    }

    public function getReceiptNumberAttribute(): ?string
    {
        return $this->relationLoaded('receipt') ? $this->receipt?->receipt_number : null;
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