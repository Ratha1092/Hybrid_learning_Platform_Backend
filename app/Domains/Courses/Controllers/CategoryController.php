<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Category;
use App\Support\ApiResponse;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->withCount('courses')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

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
                    ->latest(),
            ])
            ->withCount('courses')
            ->first();

        if (! $category) {
            return ApiResponse::error(
                'Category not found',
                404
            );
        }
        return ApiResponse::success(
            $category,
            'Category retrieved successfully'
        );
    }
}