<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonObjective extends Model
{
    protected $fillable = ['lesson_id', 'objective', 'order'];

    protected $casts = ['order' => 'integer'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
