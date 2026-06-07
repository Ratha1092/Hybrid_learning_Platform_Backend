<?php

namespace App\Jobs;

use App\Domains\Auth\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly string $action,
        public readonly ?int $userId,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
        public readonly ?array $data,
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'user_id'    => $this->userId,
            'action'     => $this->action,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'data'       => $this->data,
        ]);
    }
}
