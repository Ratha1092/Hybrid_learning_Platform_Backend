<?php

namespace App\Domains\Users\Controllers;

use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InstructorListController extends Controller
{
    public function index(Request $request)
    {
        $maxLimit = (int) Setting::get('featured_instructor_limit', 20);
        $limit = min((int) $request->integer('limit', $maxLimit), $maxLimit);

        $instructors = Cache::remember("instructors.public.{$limit}", now()->addMinutes(10), function () use ($limit) {
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
                ->orderByDesc('courses_count')
                ->limit($limit)
                ->get()
                ->map(fn ($u) => [
                    'id'       => $u->id,
                    'name'     => $u->name,
                    'avatar'   => $u->avatar,
                    'headline' => null,
                    'bio'      => $u->instructorProfile?->bio,
                    'courses'  => $u->courses_count,
                    'students' => (int) ($u->students_count ?? 0),
                ]);
        });

        return ApiResponse::success($instructors, 'Instructors retrieved successfully');
    }
}
