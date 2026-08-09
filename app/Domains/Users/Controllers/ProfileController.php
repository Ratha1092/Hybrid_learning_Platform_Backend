<?php

namespace App\Domains\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Users\Services\UserService;
use App\Domains\Users\Requests\UpdateProfileRequest;
use App\Domains\Users\Resources\UserResource;
use App\Domains\Billing\Models\BillingAddress;
use App\Domains\Learning\Models\Enrollment;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function dashboard(Request $request)
    {
        $user = $this->userService->getProfile($request->user());

        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course' => fn ($q) => $q->select(
                'id', 'title', 'slug', 'thumbnail', 'level', 'instructor_id'
            )])
            ->latest('enrolled_at')
            ->get()
            ->map(fn ($e) => [
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

        $addresses = BillingAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->get();

        $notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id'          => $n->id,
                'title'       => $n->data['title'] ?? '',
                'message'     => $n->data['message'] ?? $n->data['body'] ?? '',
                'type'        => $n->data['type'] ?? '',
                'link'        => $n->data['link'] ?? $n->data['action_url'] ?? null,
                'action_text' => $n->data['action_text'] ?? null,
                'read'        => $n->read_at !== null,
                'created_at'  => $n->created_at,
            ]);

        $stats = [
            'enrolled_courses'  => Enrollment::where('user_id', $user->id)->whereIn('status', ['active', 'completed'])->count(),
            'completed_courses' => Enrollment::where('user_id', $user->id)->where('status', 'completed')->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        return ApiResponse::success([
            'profile'       => new UserResource($user),
            'courses'       => $enrollments,
            'addresses'     => $addresses,
            'notifications' => $notifications,
            'stats'         => $stats,
        ], 'Profile dashboard retrieved successfully');
    }

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
            )
                ->withAvg(['reviews as average_rating' => fn ($qq) => $qq->where('is_approved', true)], 'rating')
                ->withCount(['reviews as reviews_count' => fn ($qq) => $qq->where('is_approved', true)])])
            ->latest('enrolled_at')
            ->get()
            ->map(fn($e) => [
                'enrollment_id'       => $e->id,
                'course_id'           => $e->course_id,
                'course_title'        => $e->course?->title,
                'course_slug'         => $e->course?->slug,
                'course_thumbnail'    => $e->course?->thumbnail_url,
                'course_level'        => $e->course?->level,
                'average_rating'      => $e->course?->average_rating ? round($e->course->average_rating, 1) : null,
                'reviews_count'       => $e->course?->reviews_count ?? 0,
                'progress_percentage' => (float) $e->progress_percentage,
                'enrolled_at'         => $e->enrolled_at,
                'completed_at'        => $e->completed_at,
            ]);

        return ApiResponse::success($enrollments, 'Enrolled courses retrieved successfully');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
        ]);

        $user = $this->userService->updateAvatar($request->user(), $request->file('avatar'));

        return ApiResponse::success(
            ['avatar_url' => $user->avatar_url],
            'Avatar updated successfully'
        );
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