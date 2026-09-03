<?php

namespace App\Domains\Community\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunityPostPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param array $post Same shape as CommunityPostController::present().
     */
    public function __construct(public array $post)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('community.posts');
    }

    public function broadcastAs(): string
    {
        return 'post.posted';
    }

    public function broadcastWith(): array
    {
        return $this->post;
    }
}
