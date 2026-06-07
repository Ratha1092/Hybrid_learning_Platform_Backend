<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use App\Domains\Auth\Models\ActivityLog;
use App\Jobs\LogActivityJob;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(
        string $action,
        ?User $user = null,
        ?Request $request = null,
        ?array $data = null
    ): void {
        // Extract strings now — Request cannot be serialized into a queue job
        $ip        = $request?->ip() ?? request()->ip();
        $userAgent = $request?->userAgent() ?? request()->userAgent();

        LogActivityJob::dispatch($action, $user?->id, $ip, $userAgent, $data);
    }

    public static function getUserHistory(User $user, int $limit = 50)
    {
        return ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public static function getAllLogs(int $limit = 100)
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public static function getRecentLogins(User $user, int $limit = 10)
    {
        return ActivityLog::where('user_id', $user->id)
            ->where('action', 'login')
            ->latest()
            ->limit($limit)
            ->get();
    }
}