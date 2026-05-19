<?php

namespace App\Domains\Payments\Models;

use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Enums\PaymentGateway;
use App\Domains\Payments\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_id',
        'external_reference',
        'idempotency_key',
        'amount',
        'currency',
        'status',
        'verification_attempts',
        'failure_reason',
        'khqr_payload',
        'payer_account',
        'meta',
        'gateway_response',
        'paid_at',
        'last_verified_at',
        'expires_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_gateway' => PaymentGateway::class,
        'status' => PaymentStatus::class,
        'verification_attempts' => 'integer',
        'meta' => 'array',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }
    public function isPaid(): bool
    {
        return in_array($this->status, [
            PaymentStatus::Paid,
            PaymentStatus::Completed,

        ], true);
    }
    public function isProcessing(): bool
    {
        return $this->status === PaymentStatus::Processing;
    }
    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::Failed;
    }
    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::Expired;
    }
    public function isKhqr(): bool
    {
        return $this->payment_gateway === PaymentGateway::KHQR;
    }
    public function isBakong(): bool
    {
        return $this->payment_gateway === PaymentGateway::BAKONG;
    }
    public function hasExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }
    public function canVerify(): bool
    {
        return $this->isPending()
            && ! $this->hasExpired();
    }
    public function canRefund(): bool
    {
        return $this->isPaid();
    }
}