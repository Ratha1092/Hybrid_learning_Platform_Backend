<?php

namespace App\Domains\Learning\Controllers;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\System\Models\Setting;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request, int $courseId): JsonResponse
    {
        $userId = optional($request->user())->id;

        $reviews = Review::where('course_id', $courseId)
            ->where(function ($q) use ($userId) {
                $q->where('is_approved', true);
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
            })
            ->with('user:id,name,avatar')
            ->latest()
            ->paginate(15);

        return ApiResponse::success($reviews, 'Reviews retrieved successfully');
    }

    public function featured(Request $request): JsonResponse
    {
        $limit = min(20, max(1, (int) $request->query('limit', 6)));

        $reviews = Review::where('is_approved', true)
            ->where('is_featured', true)
            ->with(['user:id,name,avatar', 'course:id,title,slug'])
            ->latest()
            ->take($limit)
            ->get();

        return ApiResponse::success($reviews, 'Featured reviews retrieved successfully');
    }

    public function store(Request $request, int $courseId): JsonResponse
    {
        $course = Course::find($courseId);

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $user = $request->user();

        if (!Enrollment::where('user_id', $user->id)->where('course_id', $courseId)->exists()) {
            return ApiResponse::error('You must be enrolled in this course to leave a review.', 403);
        }

        $existing = Review::withTrashed()
            ->where('course_id', $courseId)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && !$existing->trashed()) {
            return ApiResponse::error('You have already reviewed this course.', 422);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $autoApprove = !Setting::get('review_moderation_required', true)
            || Setting::get('auto_approve_reviews', false);

        $attributes = [
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_approved' => $autoApprove,
        ];

        if ($existing) {
            $existing->restore();
            $existing->update($attributes);
            $review = $existing;
        } else {
            $review = Review::create([
                'course_id' => $courseId,
                'user_id' => $user->id,
                ...$attributes,
            ]);
        }

        return ApiResponse::success(
            $review,
            $autoApprove ? 'Review submitted successfully.' : 'Review submitted and is awaiting moderation.',
            201
        );
    }
}
