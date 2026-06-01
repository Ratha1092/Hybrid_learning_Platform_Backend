<?php

namespace App\Filament\Resources\Courses\Widgets;

use App\Domains\Courses\Models\Course;
use Filament\Widgets\Widget;

class CourseStatsOverview extends Widget
{
    protected static bool $isLazy = false;
    protected string $view = 'filament.widgets.course-stats-overview';
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return ['stats' => $this->buildStats()];
    }

    private function buildStats(): array
    {
        $thisMonth = Course::withoutGlobalScopes()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = Course::withoutGlobalScopes()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $momPct = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100)
            : ($thisMonth > 0 ? 100 : 0);
        $momDesc = ($momPct >= 0 ? '+' : '') . $momPct . '% from last month';

        return [
            [
                'label'           => 'Total Courses',
                'value'           => Course::withoutGlobalScopes()->count(),
                'description'     => $momDesc,
                'icon'            => 'heroicon-o-academic-cap',
                'iconColor'       => '#a78bfa',
                'iconBg'          => 'rgba(167,139,250,0.15)',
                'chartColor'      => '#a78bfa',
                'descColor'       => '#a78bfa',
                'sparkline'       => $this->sparkline(),
            ],
            [
                'label'           => 'Pending Review',
                'value'           => Course::withoutGlobalScopes()->where('status', Course::STATUS_PENDING)->count(),
                'description'     => 'Requires your attention',
                'icon'            => 'heroicon-o-clock',
                'iconColor'       => '#fbbf24',
                'iconBg'          => 'rgba(251,191,36,0.15)',
                'chartColor'      => '#fbbf24',
                'descColor'       => '#fbbf24',
                'sparkline'       => $this->sparkline(Course::STATUS_PENDING),
            ],
            [
                'label'           => 'Published',
                'value'           => Course::withoutGlobalScopes()->where('status', Course::STATUS_PUBLISHED)->count(),
                'description'     => 'Active and visible',
                'icon'            => 'heroicon-o-check-circle',
                'iconColor'       => '#34d399',
                'iconBg'          => 'rgba(52,211,153,0.15)',
                'chartColor'      => '#34d399',
                'descColor'       => '#34d399',
                'sparkline'       => $this->sparkline(Course::STATUS_PUBLISHED),
            ],
            [
                'label'           => 'Draft',
                'value'           => Course::withoutGlobalScopes()->where('status', Course::STATUS_DRAFT)->count(),
                'description'     => 'In progress',
                'icon'            => 'heroicon-o-document-text',
                'iconColor'       => '#60a5fa',
                'iconBg'          => 'rgba(96,165,250,0.15)',
                'chartColor'      => '#60a5fa',
                'descColor'       => '#60a5fa',
                'sparkline'       => $this->sparkline(Course::STATUS_DRAFT),
            ],
            [
                'label'           => 'Archived',
                'value'           => Course::withoutGlobalScopes()->where('status', Course::STATUS_ARCHIVED)->count(),
                'description'     => 'Not visible',
                'icon'            => 'heroicon-o-archive-box',
                'iconColor'       => '#f87171',
                'iconBg'          => 'rgba(248,113,113,0.15)',
                'chartColor'      => '#f87171',
                'descColor'       => '#f87171',
                'sparkline'       => $this->sparkline(Course::STATUS_ARCHIVED),
            ],
        ];
    }

    private function sparkline(?string $status = null, int $w = 200, int $h = 48): string
    {
        $data = collect(range(6, 0))->map(function ($daysAgo) use ($status) {
            $q = Course::withoutGlobalScopes()->whereDate('created_at', now()->subDays($daysAgo));
            if ($status) {
                $q->where('status', $status);
            }
            return $q->count();
        })->toArray();

        if (count($data) < 2) {
            return "0,{$h} {$w},{$h}";
        }

        $min   = min($data);
        $max   = max($data);
        $range = max($max - $min, 1);
        $step  = $w / (count($data) - 1);
        $pad   = $h * 0.15;

        return collect($data)->map(function ($v, $i) use ($min, $range, $step, $h, $pad) {
            $x = round($i * $step, 1);
            $y = round($h - $pad - (($v - $min) / $range * ($h - $pad * 2)), 1);
            return "{$x},{$y}";
        })->implode(' ');
    }
}
