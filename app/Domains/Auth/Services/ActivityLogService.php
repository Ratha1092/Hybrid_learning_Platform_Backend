<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use App\Domains\Auth\Models\ActivityLog;
use App\Domains\System\Models\Setting;
use App\Jobs\LogActivityJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(
        string $action,
        ?User $user = null,
        ?Request $request = null,
        ?array $data = null
    ): void {
        if (!Setting::get('track_user_activity', true)) {
            return;
        }

        $ip        = $request?->ip() ?? request()->ip();
        $userAgent = $request?->userAgent() ?? request()->userAgent();
        LogActivityJob::dispatch($action, $user?->id, $ip, $userAgent, $data);
    }
    public static function logChange(
        string $action,
        ?Model $subject,
        array $oldValues = [],
        array $newValues = [],
        ?User $actor = null
    ): void {
        if (!Setting::get('track_user_activity', true)) {
            return;
        }

        $actor ??= auth()->user();

        LogActivityJob::dispatch(
            $action,
            $actor?->id,
            request()->ip(),
            request()->userAgent(),
            null,
            $subject ? $subject::class : null,
            $subject?->getKey(),
            $oldValues,
            $newValues,
        );
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