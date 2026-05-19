<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Course;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
    ];

    /**
     * Wishlist belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wishlist belongs to a course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}