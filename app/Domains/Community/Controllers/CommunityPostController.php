<?php

namespace App\Domains\Community\Controllers;

use App\Domains\Community\Events\CommunityPostDeleted;
use App\Domains\Community\Events\CommunityPostPosted;
use App\Domains\Community\Models\CommunityPost;
use App\Domains\Community\Models\CommunityPostReport;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $posts = CommunityPost::whereNull('parent_id')
            ->with([
                'user:id,name,avatar',
                'likes:id',
                'reports:id,community_post_id,user_id',
                'replies' => fn ($q) => $q->oldest(),
                'replies.user:id,name,avatar',
                'replies.likes:id',
                'replies.reports:id,community_post_id,user_id',
            ])
            ->oldest()
            ->paginate(10);

        $posts->getCollection()->transform(fn (CommunityPost $p) => $this->present($p, $userId));

        return ApiResponse::success($posts, 'Posts retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('community_posts', 'id')->where(fn ($q) => $q->whereNull('parent_id')),
            ],
        ]);

        $post = CommunityPost::create([
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => trim($validated['body']),
        ]);

        $post->load('user:id,name,avatar');

        $payload = $this->present($post, $user->id, withReplies: true);

        // liked_by_me/reported_by_me are meaningless to broadcast — specific
        // to whoever is viewing, and a brand-new post has neither for anyone.
        event(new CommunityPostPosted([...$payload, 'liked_by_me' => false, 'reported_by_me' => false]));

        return ApiResponse::success($payload, 'Posted successfully', 201);
    }

    public function like(Request $request, CommunityPost $post): JsonResponse
    {
        $user = $request->user();

        $post->likes()->toggle($user->id);

        return ApiResponse::success([
            'likes_count' => $post->likes()->count(),
            'liked_by_me' => $post->likes()->where('user_id', $user->id)->exists(),
        ], 'Like toggled successfully');
    }

    public function destroy(Request $request, CommunityPost $post): JsonResponse
    {
        $user = $request->user();

        if ((int) $post->user_id !== (int) $user->id && ! $user->isAdmin()) {
            return ApiResponse::error('You can only delete your own posts.', 403);
        }

        $id = $post->id;
        $parentId = $post->parent_id;
        $post->delete();

        event(new CommunityPostDeleted($id, $parentId));

        return ApiResponse::success(null, 'Post deleted successfully');
    }

    public function report(Request $request, CommunityPost $post): JsonResponse
    {
        $user = $request->user();

        if ((int) $post->user_id === (int) $user->id) {
            return ApiResponse::error('You cannot report your own post.', 422);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        CommunityPostReport::firstOrCreate(
            ['community_post_id' => $post->id, 'user_id' => $user->id],
            ['reason' => $validated['reason'] ?? null]
        );

        return ApiResponse::success(null, 'Post reported. Thank you.');
    }

    private function present(CommunityPost $post, ?int $userId, bool $withReplies = false): array
    {
        $likes = $post->relationLoaded('likes') ? $post->likes : collect();
        $reports = $post->relationLoaded('reports') ? $post->reports : collect();

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'parent_id' => $post->parent_id,
            'body' => $post->body,
            'likes_count' => $likes->count(),
            'liked_by_me' => $userId ? $likes->contains('id', $userId) : false,
            'reported_by_me' => $userId ? $reports->contains('user_id', $userId) : false,
            'created_at' => $post->created_at,
            'user' => $post->user,
            'replies' => $withReplies
                ? []
                : $post->replies->map(fn (CommunityPost $r) => $this->present($r, $userId))->values(),
        ];
    }
}
