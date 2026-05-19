<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class TwoFactorCode extends Model
{
    protected $table = 'two_factor_codes';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'used',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: check expiration
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
