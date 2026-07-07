<?php

namespace App\Domains\Courses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'title',
        'order',
        'deleted_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function ($section) {
            if (auth()->check()) {
                $section->deleted_by = auth()->id();
                $section->saveQuietly();
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(\App\Domains\Users\Models\User::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
}