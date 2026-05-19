<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    /**
     * Use fillable instead of guarded
     */
    protected $fillable = [
        'user_id',
        'action',
        'data',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Disable updated_at only (keep created_at)
     */
    public const UPDATED_AT = null;

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}