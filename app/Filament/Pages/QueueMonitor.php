<?php

namespace App\Filament\Pages;

use App\Support\PanelAccess;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class QueueMonitor extends Page
{
    protected string  $view = 'filament.pages.queue-monitor';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Queue Monitor';
    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';
    protected static ?int    $navigationSort = 2;
    protected static ?string $slug = 'queue-monitor';

    public static function canAccess(): bool
    {
        return PanelAccess::can('system.view_health');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function retryJob(string $uuid): void
    {
        $exitCode = \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => [$uuid]]);

        if ($exitCode !== 0) {
            Notification::make()
                ->title('Failed to queue job for retry.')
                ->body(trim(\Illuminate\Support\Facades\Artisan::output()))
                ->danger()
                ->send();
            return;
        }

        Notification::make()->title('Job queued for retry.')->success()->send();
    }

    public function deleteFailedJob(string $uuid): void
    {
        $exitCode = \Illuminate\Support\Facades\Artisan::call('queue:forget', ['id' => $uuid]);

        if ($exitCode !== 0) {
            Notification::make()
                ->title('Failed to delete job.')
                ->body(trim(\Illuminate\Support\Facades\Artisan::output()))
                ->danger()
                ->send();
            return;
        }

        Notification::make()->title('Failed job deleted.')->success()->send();
    }

    public function clearAllFailed(): void
    {
        if (Schema::hasTable('failed_jobs')) {
            DB::table('failed_jobs')->delete();
        }
        Notification::make()->title('All failed jobs cleared.')->success()->send();
    }

    protected function getViewData(): array
    {
        $hasJobsTable   = Schema::hasTable('jobs');
        $hasFailedTable = Schema::hasTable('failed_jobs');

        // Pending jobs — database driver has a `jobs` table; Redis/Horizon does not
        if ($hasJobsTable) {
            $queueStats   = DB::table('jobs')
                ->select('queue', DB::raw('count(*) as count'))
                ->groupBy('queue')
                ->orderByDesc('count')
                ->get();
            $pendingTotal = DB::table('jobs')->count();
            $activeQueues = DB::table('jobs')->distinct('queue')->count('queue');
            $oldestMin    = DB::table('jobs')->min('available_at')
                ? (int) round((time() - DB::table('jobs')->min('available_at')) / 60)
                : 0;
        } else {
            // Redis / Horizon queues
            $queueStats   = $this->redisQueueStats();
            $pendingTotal = $queueStats->sum('count');
            $activeQueues = $queueStats->where('count', '>', 0)->count();
            $oldestMin    = 0;
        }

        // Failed jobs — Laravel always writes these to the `failed_jobs` DB table by default
        if ($hasFailedTable) {
            $failedStats = DB::table('failed_jobs')
                ->select('queue', DB::raw('count(*) as count'))
                ->groupBy('queue')
                ->orderByDesc('count')
                ->get();

            $recentFailed = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(20)
                ->get(['uuid', 'queue', 'payload', 'exception', 'failed_at'])
                ->map(function ($job) {
                    $payload   = json_decode($job->payload, true);
                    $jobClass  = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown');
                    $firstLine = explode("\n", $job->exception ?? '')[0];
                    return [
                        'uuid'      => $job->uuid,
                        'queue'     => $job->queue,
                        'job'       => $jobClass,
                        'error'     => Str::limit($firstLine, 120),
                        'exception' => $job->exception,
                        'failed_at' => $job->failed_at,
                    ];
                })->all();

            $failedTotal = DB::table('failed_jobs')->count();
        } else {
            $failedStats  = collect();
            $recentFailed = [];
            $failedTotal  = 0;
        }

        $kpis = [
            'pending'        => $pendingTotal,
            'failed'         => $failedTotal,
            'queues'         => $activeQueues,
            'oldest_job_min' => $oldestMin,
            'using_redis'    => ! $hasJobsTable,
        ];

        return compact('queueStats', 'failedStats', 'recentFailed', 'kpis');
    }

    private function redisQueueStats(): \Illuminate\Support\Collection
    {
        try {
            $redis  = Redis::connection();
            $prefix = config('database.redis.options.prefix', '');
            $keys   = $redis->keys($prefix . 'queues:*');
            $stats  = [];

            foreach ($keys as $key) {
                $bare = str_replace($prefix, '', $key);
                $bare = preg_replace('/^queues:/', '', $bare);

                // Skip sub-keys like :delayed, :reserved, :notify
                if (str_contains($bare, ':')) {
                    continue;
                }

                $len = (int) $redis->llen($key);
                $stats[] = (object) ['queue' => $bare, 'count' => $len];
            }

            return collect($stats)->sortByDesc('count')->values();
        } catch (\Exception) {
            // Fallback: probe known queue names via Queue::size()
            $known = ['default', 'high', 'low', 'emails', 'notifications'];
            $stats = [];
            foreach ($known as $q) {
                try {
                    $size = Queue::size($q);
                    $stats[] = (object) ['queue' => $q, 'count' => $size];
                } catch (\Exception) {
                    // skip
                }
            }
            return collect($stats)->sortByDesc('count')->values();
        }
    }
}
