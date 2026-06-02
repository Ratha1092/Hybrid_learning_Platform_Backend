<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Finance\Models\InstructorWallet;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InstructorDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $instructorId = auth()->id();

        // Course stats
        $courses = Course::where('instructor_id', $instructorId)->get();
        $courseIds = $courses->pluck('id');

        $courseStats = [
            'total'     => $courses->count(),
            'published' => $courses->where('status', Course::STATUS_PUBLISHED)->count(),
            'draft'     => $courses->where('status', Course::STATUS_DRAFT)->count(),
            'pending'   => $courses->where('status', Course::STATUS_PENDING)->count(),
        ];

        // Student stats
        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->count();

        // Revenue stats from order_items
        $startOfMonth = now()->startOfMonth()->toDateTimeString();
        $revenueStats = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.instructor_id', $instructorId)
            ->where('orders.payment_status', 'paid')
            ->selectRaw(
                'COALESCE(SUM(order_items.instructor_amount), 0) as total_earned,
                 COALESCE(SUM(CASE WHEN order_items.created_at >= ?::timestamptz THEN order_items.instructor_amount ELSE 0 END), 0) as this_month',
                [$startOfMonth]
            )
            ->first();
        $wallet = InstructorWallet::where('instructor_id', $instructorId)->first();
        $perCourse = Course::where('instructor_id', $instructorId)
            ->withCount(['enrollments as student_count' => fn($q) => $q->where('status', 'active')])
            ->get()
            ->map(fn($c) => [
                'id'            => $c->id,
                'title'         => $c->title,
                'status'        => $c->status,
                'price'         => $c->price,
                'student_count' => $c->student_count,
                'thumbnail_url' => $c->thumbnail_url,
            ]);
        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user:id,name,email', 'course:id,title'])
            ->where('status', 'active')
            ->latest('enrolled_at')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'student_name'  => $e->user?->name ?? 'Unknown',
                'student_email' => $e->user?->email ?? '',
                'course_title'  => $e->course?->title ?? '',
                'enrolled_at'   => $e->enrolled_at,
            ]);

        return ApiResponse::success([
            'courses'            => $courseStats,
            'students'           => [
                'total_unique'   => $totalStudents,
                'total_enrolled' => $totalEnrollments,
            ],
            'revenue'            => [
                'total_earned'   => (float) $revenueStats->total_earned,
                'this_month'     => (float) $revenueStats->this_month,
                'wallet_balance' => $wallet ? (float) $wallet->balance : 0,
                'pending_payout' => $wallet ? (float) $wallet->pending_balance : 0,
            ],
            'per_course'         => $perCourse,
            'recent_enrollments' => $recentEnrollments,
        ], 'Dashboard data retrieved successfully');
    }

    public function students(): JsonResponse
    {
        $instructorId = auth()->id();

        $courseIds = Course::where('instructor_id', $instructorId)->pluck('id');

        $students = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->with(['user:id,name,email', 'course:id,title,slug,thumbnail'])
            ->latest('enrolled_at')
            ->get()
            ->map(fn($e) => [
                'student_id'          => $e->user?->id,
                'student_name'        => $e->user?->name ?? 'Unknown',
                'student_email'       => $e->user?->email ?? '',
                'course_id'           => $e->course_id,
                'course_title'        => $e->course?->title ?? '',
                'course_slug'         => $e->course?->slug ?? '',
                'progress_percentage' => (float) $e->progress_percentage,
                'enrolled_at'         => $e->enrolled_at,
                'completed_at'        => $e->completed_at,
            ]);

        return ApiResponse::success([
            'total'    => $students->count(),
            'students' => $students,
        ], 'Students retrieved successfully');
    }
}
