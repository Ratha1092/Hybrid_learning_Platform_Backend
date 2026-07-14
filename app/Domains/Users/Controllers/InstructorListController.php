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
        $maxPerPage = (int) Setting::get('featured_instructor_limit', 20);
        $perPage = max(1, min((int) $request->integer('per_page', $request->integer('limit', $maxPerPage)), $maxPerPage));
        $page = max(1, (int) $request->integer('page', 1));

        $instructors = Cache::remember("instructors.public.{$perPage}.{$page}", now()->addMinutes(10), function () use ($perPage, $page) {
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
                ->orderBy('id')
                ->paginate($perPage, ['*'], 'page', $page)
                ->through(fn ($u) => [
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
