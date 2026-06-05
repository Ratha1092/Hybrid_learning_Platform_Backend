<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)->latest()->get();
        return ApiResponse::success($courses, 'Courses retrieved successfully');
    }

    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_published', true)
            ->with('sections.lessons')
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $user = auth('sanctum')->user();
        $isEnrolled = $user && Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        $courseData = $course->toArray();
        $courseData['is_enrolled'] = $isEnrolled;

        $courseData['sections'] = $course->sections->map(function ($section) use ($isEnrolled) {
            $sectionData = $section->toArray();
            $sectionData['lessons'] = $section->lessons->map(function ($lesson) use ($isEnrolled) {
                $lessonData = $lesson->toArray();

                $canWatch = $isEnrolled || $lesson->is_preview;
                $lessonData['video_url'] = $canWatch ? $this->resolveVideoUrl($lesson) : null;

                return $lessonData;
            });
            return $sectionData;
        });

        return ApiResponse::success($courseData, 'Course retrieved successfully');
    }

    private function resolveVideoUrl($lesson): ?string
    {
        if ($lesson->video_url) {
            return $lesson->video_url;
        }

        if ($lesson->video_path) {
            return Storage::disk('public')->url($lesson->video_path);
        }

        return null;
    }
}
