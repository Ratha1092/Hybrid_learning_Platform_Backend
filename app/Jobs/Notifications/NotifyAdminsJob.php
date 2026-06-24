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
    ) {}

    public function tags(): array
    {
        return ['notifications', 'admins', class_basename($this->notificationClass)];
    }

    public function handle(): void
    {
        $notification = new $this->notificationClass(...$this->payload);

        User::role(['admin', 'super-admin'])
            ->get()
            ->each(fn (User $admin) => $admin->notify($notification));
    }
}
