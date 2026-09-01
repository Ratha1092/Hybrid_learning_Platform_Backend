<?php

namespace App\Domains\Learning\Controllers;

use App\Domains\Courses\Models\Lesson;
use App\Domains\Learning\Models\LessonComment;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LessonCommentController extends Controller
{
    public function index(Request $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('view', $lesson);

        $userId = optional($request->user())->id;

        $comments = LessonComment::where('lesson_id', $lesson->id)
            ->whereNull('parent_id')
            ->with([
                'user:id,name,avatar',
                'likes:id',
                'replies' => fn ($q) => $q->oldest(),
                'replies.user:id,name,avatar',
                'replies.likes:id',
            ])
            ->oldest()
            ->paginate(10);

        $comments->getCollection()->transform(fn (LessonComment $c) => $this->present($c, $userId));

        return ApiResponse::success($comments, 'Comments retrieved successfully');
    }

    public function store(Request $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('view', $lesson);
        $user = $request->user();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('lesson_comments', 'id')->where(
                    fn ($q) => $q->where('lesson_id', $lesson->id)->whereNull('parent_id')
                ),
            ],
            'video_timestamp' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'video_id' => [
                'nullable',
                'integer',
                Rule::exists('lesson_videos', 'id')->where(fn ($q) => $q->where('lesson_id', $lesson->id)),
            ],
        ]);

        $comment = LessonComment::create([
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => trim($validated['body']),
            'video_timestamp' => $validated['video_timestamp'] ?? null,
            'video_id' => $validated['video_id'] ?? null,
        ]);

        $comment->load('user:id,name,avatar');

        return ApiResponse::success(
            $this->present($comment, $user->id, withReplies: true),
            'Comment posted successfully',
            201
        );
    }

    public function like(Request $request, LessonComment $comment): JsonResponse
    {
        $this->authorize('view', $comment->lesson);
        $user = $request->user();

        $comment->likes()->toggle($user->id);

        return ApiResponse::success([
            'likes_count' => $comment->likes()->count(),
            'liked_by_me' => $comment->likes()->where('user_id', $user->id)->exists(),
        ], 'Like toggled successfully');
    }

    private function present(LessonComment $comment, ?int $userId, bool $withReplies = false): array
    {
        $likes = $comment->relationLoaded('likes') ? $comment->likes : collect();

        return [
            'id' => $comment->id,
            'lesson_id' => $comment->lesson_id,
            'user_id' => $comment->user_id,
            'parent_id' => $comment->parent_id,
            'body' => $comment->body,
            'video_timestamp' => $comment->video_timestamp,
            'video_id' => $comment->video_id,
            'likes_count' => $likes->count(),
            'liked_by_me' => $userId ? $likes->contains('id', $userId) : false,
            'created_at' => $comment->created_at,
            'user' => $comment->user,
            'replies' => $withReplies
                ? []
                : $comment->replies->map(fn (LessonComment $r) => $this->present($r, $userId))->values(),
        ];
    }
}
