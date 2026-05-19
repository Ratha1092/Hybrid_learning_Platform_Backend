<?php

namespace App\Policies;

use App\Domains\Courses\Models\Course;
use App\Domains\Users\Models\User;

class CoursePolicy
{
    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }
    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }
    public function manage(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }
}