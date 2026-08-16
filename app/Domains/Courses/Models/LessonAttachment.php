<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'lesson_resources';

    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'file_path',
        'deleted_by',
    ];

    protected $appends = ['file_url'];

    protected static function booted(): void
    {
        static::deleting(function (LessonAttachment $attachment) {
            if (auth()->check()) {
                $attachment->deleted_by = auth()->id();
                $attachment->saveQuietly();
            }
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getFileUrlAttribute(): string
    {
        return \Storage::disk('r2-private')->temporaryUrl($this->file_path, now()->addMinutes(30));
    }
}
