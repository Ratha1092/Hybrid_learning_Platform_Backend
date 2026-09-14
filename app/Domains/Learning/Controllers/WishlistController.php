<?php

namespace App\Domains\Learning\Controllers;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Wishlist;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $courses = Course::query()
            ->whereHas('wishlists', fn ($q) => $q->where('user_id', $user->id))
            ->with('instructor:id,name')
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_approved', true)])
            ->get(['id', 'slug', 'title', 'thumbnail_url', 'price', 'level', 'instructor_id']);

        $courses->each(function ($course) {
            $course->average_rating = $course->average_rating ? round($course->average_rating, 1) : null;
        });

        return ApiResponse::success($courses, 'Wishlist retrieved successfully');
    }

    public function toggle(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        $existing = Wishlist::where('user_id', $user->id)->where('course_id', $course->id)->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Wishlist::create(['user_id' => $user->id, 'course_id' => $course->id]);
            $wishlisted = true;
        }

        return ApiResponse::success(['wishlisted' => $wishlisted], 'Wishlist updated successfully');
    }
}
