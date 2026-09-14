<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletionRule extends Model
{
    protected $fillable = ['lesson_id', 'watch_video', 'read_content', 'pass_quiz', 'submit_assignment'];

    protected $casts = [
        'watch_video' => 'boolean', 'read_content' => 'boolean',
        'pass_quiz' => 'boolean', 'submit_assignment' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
