<?php

namespace App\Domains\Learning\Models;

use App\Domains\Courses\Models\Lesson;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'is_completed',
        'watch_time',
        'duration',
        'last_position',
        'progress_percentage',
        'completed_at',
        'last_watched_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'watch_time' => 'integer',
        'duration' => 'integer',
        'last_position' => 'integer',
        'progress_percentage' => 'decimal:2',
        'completed_at' => 'datetime',
        'last_watched_at' => 'datetime',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}