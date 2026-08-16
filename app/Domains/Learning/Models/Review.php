<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'is_approved',
        'is_featured',
        'approved_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Review $review) {
            if (auth()->check()) {
                $review->deleted_by = auth()->id();
                $review->saveQuietly();
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}