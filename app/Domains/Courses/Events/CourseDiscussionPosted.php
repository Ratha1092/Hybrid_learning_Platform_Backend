<?php

namespace App\Domains\Courses\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Queued (same as LessonCommentPosted), broadcast on a PRIVATE channel — unlike
// lesson comments, Community content is enrollment-gated, not publicly
// readable, so anyone subscribing must pass the authorizer in channels.php.
class CourseDiscussionPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $discussion Same shape as CourseDiscussionController::present().
     */
    public function __construct(public array $discussion)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("course.{$this->discussion['course_id']}.discussion");
    }

    public function broadcastAs(): string
    {
        return 'discussion.posted';
    }

    public function broadcastWith(): array
    {
        return $this->discussion;
    }
}
