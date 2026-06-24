<?php

namespace App\Support\Concerns;

use Illuminate\Support\Carbon;

/**
 * Shared date-range preset resolution, extracted from the byte-identical
 * implementations that used to live separately in Dashboard.php and Analysis.php.
 */
trait HasDateRangePresets
{
    /**
     * Resolve [from, to] Carbon instances (or nulls for all_time) from the
     * current request's `preset`, `date_from`, and `date_to` query params.
     */
    protected function resolveDateRange(string $defaultPreset = 'this_month'): array
    {
        return static::resolvePreset(
            request('preset', $defaultPreset),
            $defaultPreset,
            request('date_from'),
            request('date_to'),
        );
    }

    /**
     * Pure preset resolution with no request() dependency — used both by
     * resolveDateRange() above (interactive pages) and directly by report
     * pages' headless/scheduled code paths, where filters come from a stored
     * App\Domains\Reports\Models\ScheduledReport.filters array instead of
     * the current HTTP request.
     */
    public static function resolvePreset(string $preset, string $defaultPreset, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        if ($preset === 'custom') {
            return [
                $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null,
                $dateTo   ? Carbon::parse($dateTo)->endOfDay()     : null,
            ];
        }

        return match ($preset) {
            'today'        => [now()->startOfDay(), now()->endOfDay()],
            'yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week'    => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week'    => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'last_30'      => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'last_6m'      => [now()->subMonths(6)->startOfDay(), now()->endOfDay()],
            'this_month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'this_year'    => [now()->startOfYear(), now()->endOfYear()],
            'all_time'     => [null, null],
            default        => match ($defaultPreset) {
                'last_6m' => [now()->subMonths(6)->startOfDay(), now()->endOfDay()],
                default   => [now()->startOfMonth(), now()->endOfMonth()],
            },
        };
    }

    public static function applyDateRange($query, string $column, $from, $to)
    {
        if ($from) {
            $query->where($column, '>=', $from);
        }
        if ($to) {
            $query->where($column, '<=', $to);
        }

        return $query;
    }

    /**
     * Label list for rendering the preset dropdown, shared across report views.
     */
    public static function dateRangePresetOptions(): array
    {
        return [
            'today'        => 'Today',
            'yesterday'    => 'Yesterday',
            'this_week'    => 'This Week',
            'last_week'    => 'Last Week',
            'last_30'      => 'Last 30 Days',
            'last_6m'      => 'Last 6 Months',
            'this_month'   => 'This Month',
            'last_month'   => 'Last Month',
            'this_quarter' => 'This Quarter',
            'this_year'    => 'This Year',
            'all_time'     => 'All Time',
            'custom'       => 'Custom Range',
        ];
    }
}
