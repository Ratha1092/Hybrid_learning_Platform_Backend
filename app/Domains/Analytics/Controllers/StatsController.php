<?php

namespace App\Domains\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Support\ApiResponse;

class StatsController extends Controller
{
    public function index()
    {
        return ApiResponse::success([
            'total_students'    => User::where('role', 'student')->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_courses'     => Course::where('status', Course::STATUS_PUBLISHED)->count(),
            'total_enrollments' => Enrollment::count(),
        ], 'Platform stats retrieved successfully');
    }
}
