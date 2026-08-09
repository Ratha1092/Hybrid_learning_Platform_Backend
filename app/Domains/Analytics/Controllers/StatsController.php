<?php

namespace App\Domains\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Users\Models\User;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('stats:index', now()->addMinutes(5), fn () => [
            'total_students' => User::role('student')->count(),
            'total_instructors' => User::role('instructor')->count(),
            'total_courses' => Course::where('status', Course::STATUS_PUBLISHED)->count(),
            'total_enrollments' => Enrollment::count(),
            'top_instructor_monthly_earnings' => $this->topInstructorMonthlyEarnings(),
        ]);

        return ApiResponse::success($stats, 'Platform stats retrieved successfully');
    }

    private function topInstructorMonthlyEarnings(): float
    {
        return Cache::remember('stats:top_instructor_monthly_earnings', now()->addHour(), function () {
            $top = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.payment_status', 'paid')
                ->where('order_items.created_at', '>=', now()->startOfMonth())
                ->groupBy('order_items.instructor_id')
                ->selectRaw('SUM(order_items.instructor_amount) as total')
                ->orderByDesc('total')
                ->first();

            return (float) ($top->total ?? 0);
        });
    }
}
