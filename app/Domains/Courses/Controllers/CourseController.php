<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
public function index(Request $request)
{
    $search = $request->query('search');

    $courses = Course::with('instructor:id,name,avatar')
        ->where('is_published', true)
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('instructor', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        })
        ->latest()
        ->get();

    return ApiResponse::success($courses, 'Courses retrieved successfully');
}

    public function show($slug)
    {
        $course = Cache::remember("courses.slug.{$slug}", 3600, fn() =>
            Course::with([
                'instructor:id,name,avatar',
                'sections' => function ($q) {
                    $q->orderBy('order')->with([
                        'lessons' => function ($q) {
                            $q->orderBy('order');
                        }
                    ]);
                }
            ])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with(['sections.lessons', 'instructor:id,name,avatar'])
            ->first()
        );

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $user = auth('sanctum')->user();
        $enrollment = $user ? Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first() : null;

        $isEnrolled = (bool) $enrollment;
        $accessExpired = $enrollment ? $enrollment->isExpired() : false;
        $hasAccess = $enrollment && $enrollment->status === 'active' && !$accessExpired;

        $courseData = $course->toArray();
        $courseData['is_enrolled'] = $isEnrolled;
        $courseData['access_expired'] = $accessExpired;
        $courseData['access_expires_at'] = $enrollment?->expires_at?->toIso8601String();

        $courseData['sections'] = $course->sections->map(function ($section) use ($hasAccess) {
            $sectionData = $section->toArray();
            $sectionData['lessons'] = $section->lessons->map(function ($lesson) use ($hasAccess) {
                $lessonData = $lesson->toArray();

                $canWatch = $hasAccess || $lesson->is_preview;
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
