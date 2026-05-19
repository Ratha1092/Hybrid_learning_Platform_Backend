<?php

namespace App\Domains\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Courses\Models\Course;

class CourseSale extends Model
{
    protected $fillable = [
        'course_id',
        'total_sales',
        'total_revenue',
        'instructor_revenue',
        'platform_revenue',
    ];

    /**
     * CourseSale belongs to Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}