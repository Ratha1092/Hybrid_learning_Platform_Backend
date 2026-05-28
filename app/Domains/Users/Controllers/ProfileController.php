<?php

namespace App\Domains\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Users\Services\UserService;
use App\Domains\Users\Requests\UpdateProfileRequest;
use App\Domains\Users\Resources\UserResource;
use App\Domains\Learning\Models\Enrollment;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function me(Request $request)
    {
        $user = $this->userService->getProfile($request->user());

        return ApiResponse::success(
            new UserResource($user),
            'Profile retrieved successfully'
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success(
            new UserResource($user),
            'Profile updated successfully'
        );
    }

    public function enrolledCourses(Request $request)
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with(['course' => fn($q) => $q->select(
                'id', 'title', 'slug', 'thumbnail', 'level', 'instructor_id'
            )])
            ->latest('enrolled_at')
            ->get()
            ->map(fn($e) => [
                'enrollment_id'       => $e->id,
                'course_id'           => $e->course_id,
                'course_title'        => $e->course?->title,
                'course_slug'         => $e->course?->slug,
                'course_thumbnail'    => $e->course?->thumbnail_url,
                'course_level'        => $e->course?->level,
                'progress_percentage' => (float) $e->progress_percentage,
                'enrolled_at'         => $e->enrolled_at,
                'completed_at'        => $e->completed_at,
            ]);

        return ApiResponse::success($enrollments, 'Enrolled courses retrieved successfully');
    }

    public function checkEnrollment(Request $request, int $courseId)
    {
        $enrolled = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->exists();

        return ApiResponse::success(['enrolled' => $enrolled], 'Enrollment status retrieved');
    }
}