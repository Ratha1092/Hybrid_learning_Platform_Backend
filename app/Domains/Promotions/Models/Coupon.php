<?php

namespace App\Domains\Promotions\Models;

use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'max_uses_per_user',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'max_uses_per_user' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Coupon $coupon) {
            $coupon->code = strtoupper(trim($coupon->code));
        });
        static::deleting(function (Coupon $coupon) {
            if (auth()->check()) {
                $coupon->deleted_by = auth()->id();
                $coupon->saveQuietly();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasStarted(): bool
    {
        return !$this->starts_at || $this->starts_at->isPast();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasUsesLeft(): bool
    {
        return $this->max_uses === null || $this->used_count < $this->max_uses;
    }

    public function hasUsesLeftForUser(User $user): bool
    {
        // Only orders that were actually paid count as a "use" — pending
        // checkouts that were abandoned, expired, or cancelled must not
        // count against the per-user limit.
        return $this->orders()
            ->where('user_id', $user->id)
            ->where('payment_status', OrderPaymentStatus::Paid->value)
            ->count() < $this->max_uses_per_user;
    }

    /**
     * Whether this coupon can currently be applied at all (ignoring per-user/amount checks).
     */
    public function isRedeemable(): bool
    {
        return $this->is_active && $this->hasStarted() && !$this->isExpired() && $this->hasUsesLeft();
    }

    /**
     * Validate against a specific user and order amount. Returns an error message, or null if valid.
     */
    public function validateFor(User $user, float $amount): ?string
    {
        if (!$this->is_active) {
            return 'This coupon is no longer active.';
        }

        if (!$this->hasStarted()) {
            return 'This coupon is not active yet.';
        }

        if ($this->isExpired()) {
            return 'This coupon has expired.';
        }

        if (!$this->hasUsesLeft()) {
            return 'This coupon has reached its usage limit.';
        }

        if (!$this->hasUsesLeftForUser($user)) {
            return 'You have already used this coupon the maximum number of times.';
        }

        if ($this->min_order_amount !== null && $amount < (float) $this->min_order_amount) {
            return "This coupon requires a minimum order of " . number_format((float) $this->min_order_amount, 2) . '.';
        }

        return null;
    }

    public function calculateDiscount(float $amount): float
    {
        $discount = $this->type === self::TYPE_PERCENTAGE
            ? $amount * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $amount), 2);
    }
}
