<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAssignment extends Model
{
    protected $fillable = ['lesson_id', 'title', 'instructions', 'submission_type', 'max_score', 'is_required'];

    protected $casts = ['max_score' => 'integer', 'is_required' => 'boolean'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
