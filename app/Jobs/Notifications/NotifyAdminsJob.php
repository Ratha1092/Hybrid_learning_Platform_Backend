<?php

namespace App\Jobs\Notifications;

use App\Domains\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAdminsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30];

    public function __construct(
        public readonly string $notificationClass,
        public readonly array  $payload,
        public readonly string $permission,
    ) {}

    public function tags(): array
    {
        return ['notifications', 'admins', class_basename($this->notificationClass)];
    }

    public function handle(): void
    {
        $notification = new $this->notificationClass(...$this->payload);

        // Whoever currently holds this permission — via any role, super-admin
        // included since it's seeded with every permission — not a fixed role
        // list that silently goes stale as roles are edited.
        User::permission($this->permission)
            ->get()
            ->each(fn (User $admin) => $admin->notify($notification));
    }
}
