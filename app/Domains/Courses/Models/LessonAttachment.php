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

    protected $appends = ['file_url', 'preview_url'];

    protected static function booted(): void
    {
        static::deleting(function (LessonAttachment $attachment) {
            if (auth()->check()) {
                $attachment->deleted_by = auth()->id();
                $attachment->saveQuietly();
            }
        });

        // CourseController::show() caches the course with its lessons'
        // attachments embedded, so uploading or removing a resource has to
        // bust that entry — otherwise students don't see it for up to an hour.
        static::saved(fn (LessonAttachment $attachment) => $attachment->forgetCourseCache());
        static::deleted(fn (LessonAttachment $attachment) => $attachment->forgetCourseCache());
    }

    public function forgetCourseCache(): void
    {
        $slug = $this->lesson?->section?->course?->slug;

        if ($slug) {
            \Illuminate\Support\Facades\Cache::forget("courses.slug.{$slug}");
        }
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getFileUrlAttribute(): string
    {
        return \Storage::disk('r2-private')->temporaryUrl($this->file_path, now()->addMinutes(30));
    }

    // Same file, but asks R2 to serve it inline so the browser renders a PDF /
    // image / video in place instead of downloading it.
    public function getPreviewUrlAttribute(): string
    {
        return \Storage::disk('r2-private')->temporaryUrl(
            $this->file_path,
            now()->addMinutes(30),
            ['ResponseContentDisposition' => 'inline'],
        );
    }
}
