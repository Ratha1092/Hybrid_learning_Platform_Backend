<?php

namespace App\Domains\Courses\Controllers;

use App\Domains\Courses\Events\CourseDiscussionPosted;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\CourseDiscussion;
use App\Domains\Learning\Models\Enrollment;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseDiscussionController extends Controller
{
    public function index(Request $request, int $courseId): JsonResponse
    {
        $course = Course::find($courseId);

        if (! $course) {
            return ApiResponse::error('Course not found', 404);
        }

        $user = $request->user();

        if (! $this->canParticipate($user, $course, $courseId)) {
            return ApiResponse::error('You must be enrolled in this course to view the community.', 403);
        }

        $discussions = CourseDiscussion::where('course_id', $courseId)
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

        $discussions->getCollection()->transform(fn (CourseDiscussion $d) => $this->present($d, $course, $user?->id));

        return ApiResponse::success($discussions, 'Discussions retrieved successfully');
    }

    public function store(Request $request, int $courseId): JsonResponse
    {
        $course = Course::find($courseId);

        if (! $course) {
            return ApiResponse::error('Course not found', 404);
        }

        $user = $request->user();

        if (! $this->canParticipate($user, $course, $courseId)) {
            return ApiResponse::error('You must be enrolled in this course to post in the community.', 403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('course_discussions', 'id')->where(
                    fn ($q) => $q->where('course_id', $courseId)->whereNull('parent_id')
                ),
            ],
        ]);

        $discussion = CourseDiscussion::create([
            'course_id' => $courseId,
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => trim($validated['body']),
        ]);

        $discussion->load('user:id,name,avatar');

        $payload = $this->present($discussion, $course, $user->id, withReplies: true);

        // liked_by_me is meaningless to broadcast — specific to whoever is
        // viewing, and a brand-new post has 0 likes for everyone anyway.
        event(new CourseDiscussionPosted([...$payload, 'liked_by_me' => false]));

        return ApiResponse::success($payload, 'Posted successfully', 201);
    }

    public function like(Request $request, CourseDiscussion $discussion): JsonResponse
    {
        $course = $discussion->course;
        $user = $request->user();

        if (! $this->canParticipate($user, $course, $course->id)) {
            return ApiResponse::error('You must be enrolled in this course to like posts.', 403);
        }

        $discussion->likes()->toggle($user->id);

        return ApiResponse::success([
            'likes_count' => $discussion->likes()->count(),
            'liked_by_me' => $discussion->likes()->where('user_id', $user->id)->exists(),
        ], 'Like toggled successfully');
    }

    private function canParticipate(?\App\Domains\Users\Models\User $user, Course $course, int $courseId): bool
    {
        if (! $user) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if ((int) $course->instructor_id === (int) $user->id) {
            return true;
        }

        return Enrollment::where('user_id', $user->id)->where('course_id', $courseId)->exists();
    }

    private function present(CourseDiscussion $discussion, Course $course, ?int $userId, bool $withReplies = false): array
    {
        $likes = $discussion->relationLoaded('likes') ? $discussion->likes : collect();

        return [
            'id' => $discussion->id,
            'course_id' => $discussion->course_id,
            'user_id' => $discussion->user_id,
            'parent_id' => $discussion->parent_id,
            'body' => $discussion->body,
            'likes_count' => $likes->count(),
            'liked_by_me' => $userId ? $likes->contains('id', $userId) : false,
            'is_instructor' => (int) $discussion->user_id === (int) $course->instructor_id,
            'created_at' => $discussion->created_at,
            'user' => $discussion->user,
            'replies' => $withReplies
                ? []
                : $discussion->replies->map(fn (CourseDiscussion $r) => $this->present($r, $course, $userId))->values(),
        ];
    }
}
