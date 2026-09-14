<?php

namespace App\Domains\Learning\Models;

use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\LessonVideo;
use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonComment extends Model
{
    protected $fillable = [
        'lesson_id',
        'user_id',
        'parent_id',
        'body',
        'video_timestamp',
        'video_id',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(LessonVideo::class, 'video_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_comment_likes')->withTimestamps();
    }
}
