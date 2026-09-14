<?php

namespace App\Domains\Courses\Controllers;

use App\Domains\Courses\Models\Section;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorStandaloneSectionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $section = Section::create([
            'title'         => $validated['title'],
            'order'         => $validated['sort_order'] ?? 0,
            'instructor_id' => auth()->id(),
            'course_id'     => null,
        ]);

        return ApiResponse::success([
            'id'           => $section->id,
            'title'        => $section->title,
            'sort_order'   => $section->order,
            'course_id'    => null,
            'lessons_count' => 0,
            'lessons'      => [],
            'created_at'   => $section->created_at,
        ], 'Section created successfully', 201);
    }

    public function show($id): JsonResponse
    {
        $section = Section::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->whereNull('course_id')
            ->with(['lessons' => fn ($q) => $q->orderBy('order')])
            ->withCount('lessons')
            ->first();

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        return ApiResponse::success([
            'id'            => $section->id,
            'title'         => $section->title,
            'sort_order'    => $section->order,
            'course_id'     => null,
            'lessons_count' => $section->lessons_count,
            'lessons'       => $section->lessons,
        ], 'Section retrieved successfully');
    }

    public function update(Request $request, $id): JsonResponse
    {
        $section = Section::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->whereNull('course_id')
            ->first();

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $section->title = $validated['title'];
        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            $section->order = $validated['sort_order'];
        }
        $section->save();

        return ApiResponse::success([
            'id'            => $section->id,
            'title'         => $section->title,
            'sort_order'    => $section->order,
            'course_id'     => null,
            'lessons_count' => $section->lessons()->count(),
            'lessons'       => $section->lessons()->get(),
        ], 'Section updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $section = Section::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->whereNull('course_id')
            ->withCount('lessons')
            ->first();

        if (!$section) {
            return ApiResponse::error('Section not found', 404);
        }

        if ($section->lessons_count > 0) {
            return ApiResponse::error('Remove all lessons before deleting this section.', 422);
        }

        $section->delete();

        return ApiResponse::success(null, 'Section deleted successfully');
    }

    public function standalone(): JsonResponse
    {
        $sections = Section::where('instructor_id', auth()->id())
            ->whereNull('course_id')
            ->with('lessons')
            ->withCount('lessons')
            ->orderBy('order')
            ->get()
            ->map(fn(Section $s) => [
                'id'           => $s->id,
                'title'        => $s->title,
                'sort_order'   => $s->order,
                'course_id'    => null,
                'lessons_count' => $s->lessons_count,
                'lessons'      => $s->lessons,
            ]);

        return ApiResponse::success($sections, 'Standalone sections retrieved successfully');
    }
}
