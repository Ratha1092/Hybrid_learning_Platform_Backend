<?php

namespace App\Domains\Courses\Observers;

use App\Domains\Courses\Models\Course;

class CourseObserver
{
    public function deleted(Course $course): void
    {
        $course->sections()->each(fn ($section) => $section->delete());
    }

    public function restored(Course $course): void
    {
        $course->sections()->onlyTrashed()->each(fn ($section) => $section->restore());
    }
}
