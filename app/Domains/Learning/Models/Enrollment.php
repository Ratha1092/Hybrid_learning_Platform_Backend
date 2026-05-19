<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Orders\Models\Order;

class Enrollment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'source',
        'status',
        'progress_percentage',
        'expires_at',
        'certificate_issued',
        'enrolled_at',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'certificate_issued' => 'boolean',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function updateLastAccess(): void
    {
        $this->update([
            'last_accessed_at' => now(),
        ]);
    }
}