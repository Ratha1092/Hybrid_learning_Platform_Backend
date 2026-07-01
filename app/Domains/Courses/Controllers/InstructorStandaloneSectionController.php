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
            'created_at'   => $section->created_at,
        ], 'Section created successfully', 201);
    }

    public function standalone(): JsonResponse
    {
        $sections = Section::where('instructor_id', auth()->id())
            ->whereNull('course_id')
            ->withCount('lessons')
            ->orderBy('order')
            ->get()
            ->map(fn(Section $s) => [
                'id'           => $s->id,
                'title'        => $s->title,
                'sort_order'   => $s->order,
                'course_id'    => null,
                'lessons_count' => $s->lessons_count,
            ]);

        return ApiResponse::success($sections, 'Standalone sections retrieved successfully');
    }
}
