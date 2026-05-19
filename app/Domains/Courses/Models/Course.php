<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        'preview_video_url',
        'visibility',
        'certificate_enabled',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'approved_at' => 'datetime',
    ];
    protected $appends = [
        'thumbnail_url',
    ];
    protected static function booted(): void
    {
        static::creating(function ($course) {

            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }

            if (empty($course->status)) {
                $course->status = self::STATUS_DRAFT;
            }
        });
    }
    public function submitForReview(): void
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'is_published' => false,
        ]);
    }

    public function publish(int $adminId): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'is_published' => true,
            'approved_at' => now(),
            'approved_by' => $adminId,
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'is_published' => false,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => self::STATUS_ARCHIVED,
            'is_published' => false,
        ]);
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

        return asset('storage/' . $this->thumbnail);
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