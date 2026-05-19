<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use App\Domains\Auth\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(
        string $action,
        ?User $user = null,
        ?Request $request = null,
        ?array $data = null
    ): ?ActivityLog {
        try {
            return ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'ip_address' => $request?->ip() ?? request()->ip(),
                'user_agent' => $request?->userAgent() ?? request()->userAgent(),
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
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