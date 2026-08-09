<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('categories:index', now()->addMinutes(5), fn () => Category::query()
            ->withCount([
                'courses as courses_count' => fn ($query) => $query
                    ->published()
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get());

        return ApiResponse::success(
            $categories,
            'Categories retrieved successfully'
        );
    }

    public function show(string $slug)
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->with([
                'courses' => fn ($query) => $query
                    ->published()
                    ->latest()
                    ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
                    ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_approved', true)]),
            ])
            ->withCount('courses')
            ->first();

        if (! $category) {
            return ApiResponse::error(
                'Category not found',
                404
            );
        }

        $category->courses->each(function ($course) {
            $course->average_rating = $course->average_rating ? round($course->average_rating, 1) : null;
        });

        return ApiResponse::success(
            $category,
            'Category retrieved successfully'
        );
    }
}