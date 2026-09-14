<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonAssessment extends Model
{
    protected $fillable = ['lesson_id', 'title', 'description', 'passing_score', 'attempts', 'is_required'];

    protected $casts = ['passing_score' => 'integer', 'attempts' => 'integer', 'is_required' => 'boolean'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LessonAssessmentQuestion::class)->orderBy('order');
    }
}
