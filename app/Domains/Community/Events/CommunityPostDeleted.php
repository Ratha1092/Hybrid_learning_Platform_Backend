<?php

namespace App\Domains\Community\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunityPostDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $id, public ?int $parentId)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('community.posts');
    }

    public function broadcastAs(): string
    {
        return 'post.deleted';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->id, 'parent_id' => $this->parentId];
    }
}
