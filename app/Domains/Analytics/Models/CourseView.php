<?php

namespace App\Domains\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;

class CourseView extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'ip_address',
    ];

    /**
     * Course view belongs to course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Course view belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}