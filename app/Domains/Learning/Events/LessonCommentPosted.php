<?php

namespace App\Domains\Learning\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Queued (via Horizon, same as every other real-time push in this app)
class LessonCommentPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $comment Same shape as LessonCommentController::present().
     */
    public function __construct(public array $comment)
    {
    }

    public function broadcastOn(): Channel
    {
        // Public channel — comment reads are already publicly readable
        return new Channel("lesson.{$this->comment['lesson_id']}.comments");
    }

    public function broadcastAs(): string
    {
        return 'comment.posted';
    }

    public function broadcastWith(): array
    {
        return $this->comment;
    }
}
