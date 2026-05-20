<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Category;
use App\Support\ApiResponse;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return ApiResponse::success($categories, 'Categories retrieved successfully');
    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->with('courses')
            ->first();

        if (! $category) {
            return ApiResponse::error('Category not found', 404);
        }

        return ApiResponse::success($category, 'Category retrieved successfully');
    }
}
