<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;

class Review extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}