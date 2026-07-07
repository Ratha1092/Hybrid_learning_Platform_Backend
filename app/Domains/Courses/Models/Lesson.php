<?php

namespace App\Domains\Courses\Models;

use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Section;
use App\Domains\Learning\Models\LessonProgress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Lesson extends Model
{
    use SoftDeletes;

    public const TYPE_VIDEO = 'video';
    public const TYPE_ARTICLE = 'article';
    public const TYPE_QUIZ = 'quiz';
    public const TYPE_FILE = 'file';
    public const TYPE_LIVE = 'live';
    public const TYPE_ASSIGNMENT = 'assignment';

    protected $fillable = [
        'section_id',
        'title',
        'type',
        'description',
        'content',
        'video_url',
        'video_path',
        'video_provider',
        'attachment',
        'attachment_name',
        'quiz_data',
        'duration',
        'is_preview',
        'order',
        'deleted_by',
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'is_preview' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($lesson) {
            if (auth()->check()) {
                $lesson->deleted_by = auth()->id();
                $lesson->saveQuietly();
            }
        });
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            Section::class,
            'id',
            'id',
            'section_id',
            'course_id'
        );
    }

    public function progress(): HasMany
    {
        return $this->hasMany(
            LessonProgress::class
        );
    }
    public function isVideoLesson(): bool
    {
        return $this->type === self::TYPE_VIDEO;
    }

    public function isArticleLesson(): bool
    {
        return $this->type === self::TYPE_ARTICLE;
    }

    public function isQuizLesson(): bool
    {
        return $this->type === self::TYPE_QUIZ;
    }

    public function isLiveLesson(): bool
    {
        return $this->type === self::TYPE_LIVE;
    }

    public function isAssignmentLesson(): bool
    {
        return $this->type === self::TYPE_ASSIGNMENT;
    }

    public function isPreview(): bool
    {
        return $this->is_preview;
    }
    public function getVideoSourceAttribute(): ?string
    {
        if ($this->video_url) {
            return $this->video_url;
        }

        if ($this->video_path) {
            return \Storage::disk(config('filament.default_filesystem_disk'))->url($this->video_path);
        }

        return null;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment) {
            return null;
        }

        return \Storage::disk(config('filament.default_filesystem_disk'))->url($this->attachment);
    }
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereHas(
            'section.course',
            fn ($q) => $q->where('is_published', true)
        );
    }

    public function scopePreview(Builder $query): Builder
    {
        return $query->where(
            'is_preview',
            true
        );
    }
}