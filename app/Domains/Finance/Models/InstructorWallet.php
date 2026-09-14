<?php

namespace App\Domains\Finance\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorWallet extends Model
{
    protected $fillable = [
        'instructor_id',
        'balance',
        'pending_balance',
        'currency',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $wallet): void {
            $wallet->balance = self::normalizeValue((float) $wallet->balance);
            $wallet->pending_balance = self::normalizeValue((float) $wallet->pending_balance);
        });
    }

    public static function normalizeValue(float $value): float
    {
        return max(0.0, $value);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}