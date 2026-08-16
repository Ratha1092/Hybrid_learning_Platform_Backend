<?php

namespace App\Domains\System\Controllers;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Users\Models\User;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('home:index', now()->addMinutes(5), fn () => [
            'stats' => $this->stats(),
            'categories' => $this->categories(),
            'featured_courses' => $this->featuredCourses(),
            'top_instructors' => $this->topInstructors(),
        ]);

        return ApiResponse::success($data, 'Home data retrieved successfully');
    }

    private function stats(): array
    {
        return [
            'total_students' => User::role('student')->count(),
            'total_courses' => Course::where('status', Course::STATUS_PUBLISHED)->count(),
            'total_instructors' => User::role('instructor')->count(),
            'top_instructor_monthly_earnings' => $this->topInstructorMonthlyEarnings(),
        ];
    }

    private function topInstructorMonthlyEarnings(): float
    {
        $top = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->where('order_items.created_at', '>=', now()->startOfMonth())
            ->groupBy('order_items.instructor_id')
            ->selectRaw('SUM(order_items.instructor_amount) as total')
            ->orderByDesc('total')
            ->first();

        return (float) ($top->total ?? 0);
    }

    private function categories()
    {
        return Category::query()
            ->withCount([
                'courses as courses_count' => fn ($query) => $query->published(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function featuredCourses()
    {
        $courses = Course::with('instructor:id,name,avatar')
            ->withAvg(['reviews as average_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->withCount([
                'reviews as reviews_count' => fn ($q) => $q->where('is_approved', true),
                'sections',
                'enrollments as students_count',
            ])
            ->withSum('lessons as total_duration_seconds', 'duration')
            ->where('is_published', true)
            ->latest()
            ->take(9)
            ->get();

        $courses->each(function ($course) {
            $course->average_rating = $course->average_rating ? round($course->average_rating, 1) : null;
        });

        return $courses;
    }

    private function topInstructors()
    {
        return User::role('instructor')
            ->whereHas('courses', fn ($q) => $q->where('is_published', true))
            ->withCount(['courses' => fn ($q) => $q->where('is_published', true)])
            ->addSelect([
                'students_count' => DB::table('enrollments')
                    ->join('courses', 'courses.id', '=', 'enrollments.course_id')
                    ->whereColumn('courses.instructor_id', 'users.id')
                    ->where('courses.is_published', true)
                    ->selectRaw('count(*)'),
            ])
            ->with('instructorProfile:user_id,bio,website')
            ->orderByDesc('students_count')
            ->take(8)
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->avatar,
                'avatar_url' => $u->avatar_url,
                'bio' => $u->instructorProfile?->bio,
                'courses' => $u->courses_count,
                'students' => (int) ($u->students_count ?? 0),
            ]);
    }
}
