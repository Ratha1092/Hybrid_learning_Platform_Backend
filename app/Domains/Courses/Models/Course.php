<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Section;
use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Tag;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Analytics\Models\CourseView;
use App\Domains\Finance\Models\CourseSale;

class Course extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending_review';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'preview_video_path',
        'price',
        'level',
        'language',
        'duration',
        'requirements',
        'what_you_will_learn',
        'status',
        'is_published',
        'approved_at',
        'approved_by',
        'commission_percentage',
        'visibility',
        'rejection_reason',
        'deleted_by',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'approved_at' => 'datetime',
    ];
    protected $appends = [
        'thumbnail_url',
        'preview_video_url',
    ];
    protected static function booted(): void
    {
        static::creating(function ($course) {

            if (empty($course->slug)) {
                $base = Str::slug($course->title);
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $course->slug = $slug;
            }

            if (empty($course->status)) {
                $course->status = self::STATUS_DRAFT;
            }
        });
        static::saving(function ($course) {
            $course->is_published = $course->status === self::STATUS_PUBLISHED;
            if ($course->exists) {
                Cache::forget("courses.slug.{$course->getOriginal('slug')}");
                Cache::forget("courses.slug.{$course->slug}");
            }

            if ($course->isDirty(['is_published', 'status'])) {
                Cache::forget('courses.published');
            }
        });
        static::deleting(function ($course) {
            if (auth()->check()) {
                $course->deleted_by = auth()->id();
                $course->saveQuietly();
            }
        });
    }
    public function submitForReview(): void
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'is_published' => false,
        ]);
        Cache::tags(['dashboard'])->flush();
        Cache::forget('courses.published');
    }

    public function publish(int $adminId): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'is_published' => true,
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);
        Cache::tags(['dashboard'])->flush();
        Cache::forget('courses.published');
        Cache::forget("courses.slug.{$this->slug}");
    }

    public function reject(?string $reason = null): void
    {
        $this->update([
            'status'           => self::STATUS_REJECTED,
            'is_published'     => false,
            'rejection_reason' => $reason,
        ]);
        Cache::tags(['dashboard'])->flush();
        Cache::forget('courses.published');
    }

    public function archive(): void
    {
        $this->update([
            'status' => self::STATUS_ARCHIVED,
            'is_published' => false,
        ]);
        Cache::tags(['dashboard'])->flush();
        Cache::forget('courses.published');
        Cache::forget("courses.slug.{$this->slug}");
    }

    /**
     * Restores an archived course directly back to Published — unlike
     * returnToDraft(), this doesn't require the course to go through
     * review again, since it was already approved before being archived.
     */
    public function unarchive(int $adminId): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'is_published' => true,
            'approved_at' => now(),
            'approved_by' => $adminId,
        ]);
        Cache::tags(['dashboard'])->flush();
        Cache::forget('courses.published');
        Cache::forget("courses.slug.{$this->slug}");
    }
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'instructor_id'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)
            ->orderBy('order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,
            Section::class
        );
    }

    public function enrollments()
    {
        return $this->hasMany(
            \App\Domains\Learning\Models\Enrollment::class
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(CourseView::class);
    }

    public function sales(): HasOne
    {
        return $this->hasOne(CourseSale::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPendingReview(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }
    public function canBePublished(): bool
    {
        return
            $this->sections()->exists()
            && $this->lessons()->exists()
            && !empty($this->title)
            && !empty($this->description);
    }
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail) {
            return null;
        }

        return \Storage::disk('r2')->url($this->thumbnail);
    }
    public function getPreviewVideoUrlAttribute(): ?string
    {
        if (!$this->preview_video_path) {
            return null;
        }

        return \Storage::disk('r2-private')->temporaryUrl($this->preview_video_path, now()->addMinutes(30));
    }
    public function scopePublished($query)
    {
        return $query->where(
            'status',
            self::STATUS_PUBLISHED
        );
    }
    public function scopeDraft($query)
    {
        return $query->where(
            'status',
            self::STATUS_DRAFT
        );
    }
    public function scopePendingReview($query)
    {
        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }
}
