<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
        'token',
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
     * Helper method (very useful)
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
