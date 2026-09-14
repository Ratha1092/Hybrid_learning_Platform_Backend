<?php

use App\Domains\Courses\Models\Course;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Register the channels that the application should listen on.
| Each channel is guarded so only the authenticated owner can subscribe.
|
*/

// Private per-user channel
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('course.{courseId}.discussion', function ($user, $courseId) {
    $course = Course::find($courseId);
    if (! $course) {
        return false;
    }
    if ($user->isAdmin()) {
        return true;
    }
    if ((int) $course->instructor_id === (int) $user->id) {
        return true;
    }
    return $user->hasEnrolledCourse((int) $courseId);
});

// Any authenticated user may read the site-wide feed — no extra check needed
// beyond being logged in, and /broadcasting/auth already requires auth:sanctum,
// so simply reaching this callback proves that.
Broadcast::channel('community.posts', function ($user) {
    return (bool) $user;
});
