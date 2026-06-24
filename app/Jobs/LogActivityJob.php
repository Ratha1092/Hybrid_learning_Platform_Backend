<?php

namespace App\Jobs;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Users\Models\User;
use App\Jobs\Mail\SendSecurityAlertEmailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function tags(): array
    {
        return ['activity', "action:{$this->action}", "user:{$this->userId}"];
    }

    public function __construct(
        public readonly string $action,
        public readonly ?int $userId,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
        public readonly ?array $data,
        public readonly ?string $subjectType = null,
        public readonly ?int $subjectId = null,
        public readonly ?array $oldValues = null,
        public readonly ?array $newValues = null,
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'user_id'      => $this->userId,
            'action'       => $this->action,
            'subject_type' => $this->subjectType,
            'subject_id'   => $this->subjectId,
            'old_values'   => $this->oldValues,
            'new_values'   => $this->newValues,
            'ip_address'   => $this->ipAddress,
            'user_agent'   => $this->userAgent,
            'data'         => $this->data,
        ]);

        if ($this->action === 'failed_login' && $this->ipAddress) {
            $this->maybeSendSecurityAlert();
        }
    }

    private function maybeSendSecurityAlert(): void
    {
        $cacheKey = 'security_alert_sent_' . md5($this->ipAddress);
        if (Cache::has($cacheKey)) {
            return;
        }

        $recentCount = ActivityLog::where('action', 'failed_login')
            ->where('ip_address', $this->ipAddress)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recentCount < 5) {
            return;
        }

        Cache::put($cacheKey, true, now()->addHour());

        $recentAttempts = ActivityLog::with('user')
            ->where('action', 'failed_login')
            ->where('ip_address', $this->ipAddress)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($log) => [
                'user'  => $log->user?->name ?? '—',
                'email' => $log->user?->email ?? '—',
                'time'  => $log->created_at?->format('H:i:s'),
                'ua'    => $log->user_agent,
            ])
            ->all();

        User::role('super-admin')->each(
            fn ($admin) => SendSecurityAlertEmailJob::dispatch(
                $this->ipAddress,
                $recentCount,
                $recentAttempts,
                $admin->email,
                $admin->name
            )
        );
    }
}
