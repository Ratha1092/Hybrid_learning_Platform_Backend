<?php

namespace App\Domains\Notifications\Notifications;

use App\Domains\Learning\Models\LessonComment;
use App\Domains\Notifications\Concerns\BroadcastsAsNotification;
use App\Domains\Notifications\Enums\NotificationType;
use App\Domains\Notifications\Support\NotificationChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CommentReplyNotification extends Notification
{
    use BroadcastsAsNotification;
    use Queueable;

    public function __construct(
        public readonly LessonComment $reply,
        public readonly string $lessonTitle,
        public readonly string $courseSlug,
    ) {}

    public function via(object $notifiable): array
    {
        return NotificationChannels::standard();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    private function payload(): array
    {
        $replierName = $this->reply->user?->name ?? 'Someone';

        return [
            'title'       => 'New reply to your comment',
            'message'     => "{$replierName} replied to your comment on \"{$this->lessonTitle}\".",
            'type'        => NotificationType::DISCUSSION->value,
            'comment_id'  => $this->reply->parent_id,
            'reply_id'    => $this->reply->id,
            // Deep-links straight to the lesson and opens the exact comment
            // thread the reply landed on, instead of dumping the reader on
            // the course's first lesson (see Learn.tsx's `lesson`/`comment`
            // query-param handling).
            'link'        => env('FRONTEND_URL', 'http://localhost:3000')
                . "/learn/{$this->courseSlug}?lesson={$this->reply->lesson_id}&comment={$this->reply->parent_id}",
            'action_text' => 'View reply',
        ];
    }
}
