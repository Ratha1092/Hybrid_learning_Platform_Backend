<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAssessmentQuestion extends Model
{
    protected $fillable = [
        'assessment_id', 'question', 'type', 'options', 'correct_options',
        'explanation', 'points', 'order',
    ];

    protected $casts = [
        'options' => 'array', 'correct_options' => 'array',
        'points' => 'integer', 'order' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(LessonAssessment::class, 'assessment_id');
    }
}
