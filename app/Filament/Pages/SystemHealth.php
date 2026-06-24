<?php

namespace App\Filament\Pages;

use App\Support\PanelAccess;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemHealth extends Page
{
    protected string  $view = 'filament.pages.system-health';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-server';
    protected static ?string $navigationLabel = 'System Health';
    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';
    protected static ?int    $navigationSort = 4;
    protected static ?string $slug = 'system-health';

    public static function canAccess(): bool
    {
        return PanelAccess::can('system.view_health');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function clearFailedJobs(): void
    {
        DB::table('failed_jobs')->delete();
        session()->flash('success', 'Failed jobs cleared.');
    }

    protected function getViewData(): array
    {
        return [
            'queue'      => $this->queueStats(),
            'database'   => $this->databaseStats(),
            'cache'      => $this->cacheStats(),
            'storage'    => $this->storageStats(),
            'logs'       => $this->logStats(),
            'failedJobs' => $this->recentFailedJobs(),
            'app'        => $this->appInfo(),
        ];
    }

    private function queueStats(): array
    {
        try {
            return [
                'pending' => DB::table('jobs')->count(),
                'failed'  => DB::table('failed_jobs')->count(),
                'status'  => 'ok',
            ];
        } catch (\Throwable $e) {
            return ['pending' => 0, 'failed' => 0, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }

    private function databaseStats(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $pingMs = round((microtime(true) - $start) * 1000, 2);

            return [
                'ping_ms' => $pingMs,
                'driver'  => DB::getDriverName(),
                'status'  => 'ok',
            ];
        } catch (\Throwable $e) {
            return ['ping_ms' => null, 'driver' => '?', 'status' => 'error', 'error' => $e->getMessage()];
        }
    }

    private function cacheStats(): array
    {
        try {
            $key = 'sys_health_check_' . time();
            Cache::put($key, 'ok', 5);
            $val = Cache::get($key);
            Cache::forget($key);

            return [
                'status' => $val === 'ok' ? 'ok' : 'fail',
                'driver' => config('cache.default'),
            ];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'driver' => config('cache.default'), 'error' => $e->getMessage()];
        }
    }

    private function storageStats(): array
    {
        $path = storage_path();
        try {
            $free  = disk_free_space($path);
            $total = disk_total_space($path);
            $used  = $total - $free;

            return [
                'free_gb'  => round($free / 1073741824, 2),
                'total_gb' => round($total / 1073741824, 2),
                'used_pct' => $total > 0 ? round(($used / $total) * 100, 1) : 0,
                'status'   => 'ok',
            ];
        } catch (\Throwable $e) {
            return ['free_gb' => 0, 'total_gb' => 0, 'used_pct' => 0, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }

    private function logStats(): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (! file_exists($logPath)) {
            return ['size_mb' => 0, 'errors_24h' => 0, 'status' => 'ok'];
        }

        $sizeMb = round(filesize($logPath) / 1048576, 2);

        // Count ERROR lines in last 500 lines
        $errors = 0;
        try {
            $file = new \SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $total = $file->key();
            $file->seek(max(0, $total - 500));
            $cutoff = now()->subDay()->format('Y-m-d H:i:s');
            while (! $file->eof()) {
                $line = $file->fgets();
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*\.(ERROR|CRITICAL|ALERT|EMERGENCY):/', $line, $m)) {
                    if ($m[1] >= $cutoff) {
                        $errors++;
                    }
                }
            }
        } catch (\Throwable) {}

        return [
            'size_mb'     => $sizeMb,
            'errors_24h'  => $errors,
            'status'      => $sizeMb > 100 ? 'warn' : 'ok',
        ];
    }

    private function recentFailedJobs(): array
    {
        try {
            return DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(10)
                ->get(['id', 'queue', 'payload', 'exception', 'failed_at'])
                ->map(function ($job) {
                    $payload   = json_decode($job->payload, true);
                    $jobClass  = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown');
                    $exception = \Illuminate\Support\Str::limit($job->exception ?? '', 300);
                    return [
                        'id'        => $job->id,
                        'queue'     => $job->queue,
                        'job'       => $jobClass,
                        'exception' => $exception,
                        'failed_at' => $job->failed_at,
                    ];
                })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function appInfo(): array
    {
        return [
            'env'     => config('app.env'),
            'debug'   => config('app.debug'),
            'version' => app()->version(),
            'php'     => PHP_VERSION,
            'now'     => now()->format('Y-m-d H:i:s T'),
        ];
    }
}
