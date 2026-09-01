<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonVideo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'video_url',
        'video_path',
        'duration',
        'order',
    ];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
    ];

    protected $appends = ['video_source'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getVideoSourceAttribute(): ?string
    {
        if ($this->video_url) {
            return $this->video_url;
        }

        if ($this->video_path) {
            return \Storage::disk('r2-private')->temporaryUrl($this->video_path, now()->addMinutes(30));
        }

        return null;
    }
}
