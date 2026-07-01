<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Category;
use App\Domains\Users\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q       = trim($request->string('q'));
        $type    = $request->string('type', 'all'); // all|courses|instructors|categories
        $limit   = min((int) $request->integer('limit', 10), 50);

        if (strlen($q) < 2) {
            return ApiResponse::error('Query must be at least 2 characters.', 422);
        }

        $cacheKey = "search.{$type}.{$limit}." . md5($q);
        $results  = Cache::remember($cacheKey, 60, fn () => $this->runSearch($q, $type, $limit));

        return ApiResponse::success($results, 'Search results');
    }

    private function runSearch(string $q, string $type, int $limit): array
    {
        $results = [];

        if ($type === 'all' || $type === 'courses') {
            $results['courses'] = Course::with('instructor:id,name,avatar', 'category:id,name')
                ->where('is_published', true)
                ->where(function ($query) use ($q) {
                    $query->where('title', 'ilike', "%{$q}%")
                          ->orWhere('short_description', 'ilike', "%{$q}%")
                          ->orWhereHas('instructor', fn ($i) => $i->where('name', 'ilike', "%{$q}%"))
                          ->orWhereHas('category',   fn ($c) => $c->where('name', 'ilike', "%{$q}%"));
                })
                ->withCount('enrollments')
                ->orderByDesc('enrollments_count')
                ->limit($limit)
                ->get()
                ->map(fn ($c) => [
                    'id'          => $c->id,
                    'title'       => $c->title,
                    'slug'        => $c->slug,
                    'thumbnail'   => $c->thumbnail_url,
                    'price'       => (float) $c->price,
                    'instructor'  => ['id' => $c->instructor?->id, 'name' => $c->instructor?->name, 'avatar' => $c->instructor?->avatar],
                    'category'    => ['id' => $c->category?->id,   'name' => $c->category?->name],
                    'enrollments' => $c->enrollments_count,
                    'type'        => 'course',
                ]);
        }

        if ($type === 'all' || $type === 'instructors') {
            $results['instructors'] = User::role('instructor')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'ilike', "%{$q}%")
                          ->orWhere('email', 'ilike', "%{$q}%")
                          ->orWhereHas('instructorProfile', fn ($p) => $p->where('bio', 'ilike', "%{$q}%"));
                })
                ->withCount(['courses' => fn ($c) => $c->where('is_published', true)])
                ->orderByDesc('courses_count')
                ->limit($limit)
                ->get()
                ->map(fn ($u) => [
                    'id'      => $u->id,
                    'name'    => $u->name,
                    'avatar'  => $u->avatar,
                    'courses' => $u->courses_count,
                    'type'    => 'instructor',
                ]);
        }

        if ($type === 'all' || $type === 'categories') {
            $results['categories'] = Category::where(function ($query) use ($q) {
                    $query->where('name', 'ilike', "%{$q}%")
                          ->orWhere('description', 'ilike', "%{$q}%");
                })
                ->withCount(['courses' => fn ($c) => $c->where('is_published', true)])
                ->orderByDesc('courses_count')
                ->limit($limit)
                ->get()
                ->map(fn ($c) => [
                    'id'      => $c->id,
                    'name'    => $c->name,
                    'slug'    => $c->slug,
                    'icon'    => $c->icon,
                    'courses' => $c->courses_count,
                    'type'    => 'category',
                ]);
        }

        $results['query'] = $q;
        $results['total'] = collect($results)->except('query', 'total')
            ->flatten(1)
            ->count();

        return $results;
    }
}
