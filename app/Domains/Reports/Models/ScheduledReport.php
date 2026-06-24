<?php

namespace App\Domains\Reports\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ScheduledReport extends Model
{
    protected $fillable = [
        'report_type',
        'frequency',
        'format',
        'recipient_emails',
        'filters',
        'created_by',
        'is_active',
        'last_run_at',
        'next_run_at',
    ];

    protected $casts = [
        'recipient_emails' => 'array',
        'filters' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public const REPORT_LABELS = [
        'executive' => 'Executive Dashboard',
        'revenue' => 'Revenue',
        'payment' => 'Payment',
        'payout' => 'Payout',
        'course_intelligence' => 'Course Intelligence',
        'instructor_intelligence' => 'Instructor Intelligence',
        'learning_intelligence' => 'Learning Intelligence',
        'audit' => 'Audit',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDue($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            });
    }

    public function computeNextRunAt(): Carbon
    {
        return match ($this->frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            default => now()->addDay(),
        };
    }

    public function reportLabel(): string
    {
        return self::REPORT_LABELS[$this->report_type] ?? ucfirst(str_replace('_', ' ', $this->report_type));
    }
}
