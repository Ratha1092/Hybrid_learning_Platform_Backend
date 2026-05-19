<?php

namespace App\Domains\Learning\Controllers;

use App\Http\Controllers\Controller;

use App\Domains\Courses\Models\Lesson;
use App\Domains\Learning\Services\ProgressService;

use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function __construct(
        private readonly ProgressService $progressService
    ) {}

    public function show(Request $request, Lesson $lesson)
    {
        $progress = $this->progressService->getProgress(
            user: $request->user(),
            lesson: $lesson,
        );

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'watch_time' => ['nullable', 'integer', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'last_position' => ['nullable', 'integer', 'min:0'],
            'is_completed' => ['nullable', 'boolean'],
        ]);

        $progress = $this->progressService->updateProgress(
            user: $request->user(),
            lesson: $lesson,
            data: $validated,
        );

        return response()->json([
            'success' => true,
            'message' => 'Lesson progress updated successfully.',
            'data' => $progress,
        ]);
    }
}