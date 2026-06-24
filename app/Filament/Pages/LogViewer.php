<?php

namespace App\Filament\Pages;

use App\Support\PanelAccess;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class LogViewer extends Page
{
    protected string  $view = 'filament.pages.log-viewer';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Log Viewer';
    protected static string|\UnitEnum|null $navigationGroup   = 'Monitoring';
    protected static ?int    $navigationSort = 1;
    protected static ?string $slug = 'log-viewer';

    private const LOG_FILES = [
        'laravel'  => 'laravel.log',
        'schedule' => 'schedule.log',
    ];

    private const LEVEL_COLORS = [
        'emergency' => '#ef4444',
        'alert'     => '#ef4444',
        'critical'  => '#ef4444',
        'error'     => '#f87171',
        'warning'   => '#f59e0b',
        'notice'    => '#3b82f6',
        'info'      => '#10b981',
        'debug'     => '#6366f1',
    ];

    public static function canAccess(): bool
    {
        return PanelAccess::can('system.view_logs');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $file       = request('file', 'laravel');
        $level      = strtolower(request('level', 'all'));
        $search     = trim(request('search', ''));
        $date       = request('date', '');
        $page       = max(1, (int) request('page', 1));
        $perPage    = 50;

        if (! array_key_exists($file, self::LOG_FILES)) {
            $file = 'laravel';
        }

        $logPath = storage_path('logs/' . self::LOG_FILES[$file]);
        $entries = $this->parseLog($logPath);

        // Filter
        if ($level !== 'all') {
            $entries = array_filter($entries, fn($e) => strtolower($e['level']) === $level);
        }
        if ($search !== '') {
            $entries = array_filter($entries, fn($e) =>
                Str::contains(strtolower($e['message'] . ' ' . $e['context']), strtolower($search))
            );
        }
        if ($date !== '') {
            $entries = array_filter($entries, fn($e) => Str::startsWith($e['datetime'], $date));
        }

        $entries    = array_values($entries);
        $total      = count($entries);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $entries    = array_slice($entries, ($curPage - 1) * $perPage, $perPage);

        $levelCounts = $this->countLevels($logPath);

        return compact(
            'entries', 'total', 'totalPages', 'curPage', 'perPage',
            'file', 'level', 'search', 'date', 'levelCounts'
        );
    }

    private function parseLog(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        // Read last 2000 lines efficiently
        $lines = $this->tailFile($path, 2000);

        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+?)(\{.*\})?$/s';

        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)$/', $line, $m)) {
                if ($current !== null) {
                    $entries[] = $current;
                }
                $rest    = $m[4];
                $context = '';
                // Try to split JSON context from message
                $jsonStart = strrpos($rest, ' {');
                if ($jsonStart !== false) {
                    $context = substr($rest, $jsonStart + 1);
                    $rest    = substr($rest, 0, $jsonStart);
                }
                $current = [
                    'datetime'  => $m[1],
                    'env'       => $m[2],
                    'level'     => strtolower($m[3]),
                    'message'   => trim($rest),
                    'context'   => trim($context),
                    'trace'     => [],
                    'color'     => self::LEVEL_COLORS[strtolower($m[3])] ?? '#94a3b8',
                ];
            } elseif ($current !== null) {
                $trimmed = trim($line);
                if ($trimmed !== '' && $trimmed !== '[stacktrace]') {
                    $current['trace'][] = $trimmed;
                }
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        // Newest first
        return array_reverse($entries);
    }

    private function tailFile(string $path, int $lines): array
    {
        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $total = $file->key();

        $start = max(0, $total - $lines);
        $file->seek($start);

        $result = [];
        while (! $file->eof()) {
            $result[] = rtrim($file->fgets(), "\r\n");
        }

        return $result;
    }

    private function countLevels(string $path): array
    {
        $counts = ['error' => 0, 'warning' => 0, 'info' => 0, 'debug' => 0];
        if (! file_exists($path)) {
            return $counts;
        }

        $lines = $this->tailFile($path, 2000);
        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}[^\]]+\] \w+\.(\w+):/', $line, $m)) {
                $lvl = strtolower($m[1]);
                if (in_array($lvl, ['emergency', 'alert', 'critical', 'error'])) {
                    $counts['error']++;
                } elseif ($lvl === 'warning') {
                    $counts['warning']++;
                } elseif ($lvl === 'info' || $lvl === 'notice') {
                    $counts['info']++;
                } elseif ($lvl === 'debug') {
                    $counts['debug']++;
                }
            }
        }

        return $counts;
    }
}
