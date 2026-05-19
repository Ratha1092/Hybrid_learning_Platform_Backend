<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Support\ApiResponse;

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

        return ApiResponse::success($course, 'Course retrieved successfully');
    }
}
