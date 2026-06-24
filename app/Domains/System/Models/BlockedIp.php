<?php

namespace App\Domains\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class BlockedIp extends Model
{
    protected $fillable = ['ip_address', 'reason', 'blocked_by', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public static function isBlocked(string $ip): bool
    {
        return static::where('ip_address', $ip)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();
    }
}
