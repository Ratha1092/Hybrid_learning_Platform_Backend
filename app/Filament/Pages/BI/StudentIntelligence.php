<?php

namespace App\Filament\Pages\BI;

use App\Domains\Analytics\Models\DailyMetric;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasBiCsvExport;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class StudentIntelligence extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.student-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Student Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'bi/students';

    public string $preset = 'this_month';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->preset   = request('preset', 'this_month');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');
    }

    public function applyDateFilter(string $preset, string $from = '', string $to = ''): void
    {
        $this->preset   = $preset ?: 'this_month';
        $this->dateFrom = $preset === 'custom' ? $from : '';
        $this->dateTo   = $preset === 'custom' ? $to : '';
        $this->dispatchDateResolved();
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable { return ''; }

    public static function canAccess(): bool
    {
        return PanelAccess::can('bi.view_students');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.student-intelligence';
    }

    public static function pdfViewData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Student Intelligence Report',
            'filtersSummary' => static::dateRangePresetOptions()[$preset] ?? ucfirst($preset),
        ], static::buildPageData($filters));
    }

    public function getViewData(): array
    {
        $filters = ['preset' => $this->preset ?: 'this_month', 'date_from' => $this->dateFrom ?: null, 'date_to' => $this->dateTo ?: null];
        [$from, $to] = static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'], $filters['date_to']);
        return array_merge(
            ['activePreset' => $filters['preset'], 'activeDateFrom' => $from?->format('Y-m-d') ?? '', 'activeDateTo' => $to?->format('Y-m-d') ?? ''],
            static::buildPageData($filters)
        );
    }

    public static function buildPageData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $cacheKey = 'bi.students.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            $newStudents    = (int) static::applyDateRange(User::role('student'), 'created_at', $from, $to)->count();
            $activeLearners = (int) static::applyDateRange(
                LessonProgress::query()->selectRaw('COUNT(DISTINCT user_id) as cnt'), 'last_watched_at', $from, $to
            )->value('cnt') ?? 0;

            // Returning: had enrollments before $from, active in period
            $returningStudents = $from
                ? (int) Enrollment::where('enrolled_at', '<', $from)
                    ->whereHas('user', fn ($q) => $q->role('student'))
                    ->whereIn('user_id', function ($sub) use ($from, $to) {
                        $sub->select('user_id')->from('lesson_progress')
                            ->when($from, fn ($q) => $q->where('last_watched_at', '>=', $from))
                            ->when($to, fn ($q) => $q->where('last_watched_at', '<', Carbon::instance($to)->addDay()->startOfDay()));
                    })
                    ->distinct('user_id')->count('user_id')
                : 0;

            $totalEnrollments    = Enrollment::count();
            $completedEnrollments = Enrollment::where('status', 'completed')->count();
            $completionRate      = $totalEnrollments > 0 ? round($completedEnrollments / $totalEnrollments * 100, 1) : 0.0;
            $totalWatchHours     = round((float) LessonProgress::sum('watch_time') / 3600, 1);
            $distinctLearners    = (int) LessonProgress::distinct('user_id')->count('user_id');
            $avgWatchHours       = $distinctLearners > 0 ? round($totalWatchHours / $distinctLearners, 1) : 0.0;

            // Dropout rate: enrolled >30 days ago, never completed, no recent activity
            $dropoutCandidates = Enrollment::where('status', '!=', 'completed')
                ->where('enrolled_at', '<', now()->subDays(30))
                ->whereDoesntHave('user', fn ($q) => $q->whereHas('lessonProgress', fn ($lp) =>
                    $lp->where('last_watched_at', '>=', now()->subDays(30))
                ))
                ->count();
            $dropoutRate = $totalEnrollments > 0 ? round($dropoutCandidates / $totalEnrollments * 100, 1) : 0.0;

            // Student growth 12 months from DailyMetric
            $trendEnd   = $to ?? now();
            $trendStart = (clone $trendEnd)->subMonths(11)->startOfMonth();
            $rawGrowth  = DailyMetric::selectRaw("DATE_TRUNC('month', date) as month, SUM(new_users) as new_u")
                ->whereBetween('date', [$trendStart->toDateString(), $trendEnd->toDateString()])
                ->groupByRaw("DATE_TRUNC('month', date)")->orderBy('month')->get()
                ->keyBy(fn ($r) => Carbon::parse($r->month)->format('Y-m'));

            $growthLabels = [];
            $growthValues = [];
            for ($i = 11; $i >= 0; $i--) {
                $month         = now()->subMonths($i)->startOfMonth();
                $key           = $month->format('Y-m');
                $growthLabels[] = $month->format("M 'y");
                $growthValues[] = (int) ($rawGrowth[$key]->new_u ?? 0);
            }

            // Learning hours (6 months)
            $hoursLabels = [];
            $hoursValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $m    = now()->subMonths($i)->startOfMonth();
                $mEnd = (clone $m)->endOfMonth();
                $hoursLabels[] = $m->format("M 'y");
                $hoursValues[] = round((float) LessonProgress::whereBetween('last_watched_at', [$m, $mEnd])->sum('watch_time') / 3600, 1);
            }

            // Active vs dormant for donut — status-based and all-time on both sides
            // deliberately, so the pair always sums to totalStudents regardless of
            // the date filter. (Previously this donut paired the period-filtered
            // activeLearners with totalStudents-activeLearners as "dormant", which
            // made "dormant" swing with whatever date range happened to be selected
            // instead of reflecting actual account status.)
            $activeCount  = (int) User::role('student')->where('status', 'active')->count();
            $dormantCount = (int) User::role('student')->where('status', '!=', 'active')->count();

            $totalStudents = (int) User::role('student')->count();

            // Most active students (by watch time)
            $mostActive = LessonProgress::selectRaw('user_id, SUM(watch_time) as total_watch, COUNT(DISTINCT course_id) as enrolled')
                ->groupBy('user_id')->orderByRaw('SUM(watch_time) DESC')->take(10)
                ->with('user:id,name')->get()
                ->map(fn ($r) => [
                    'name'      => $r->user?->name ?? '—',
                    'hours'     => round($r->total_watch / 3600, 1),
                    'enrolled'  => (int) $r->enrolled,
                    'completed' => Enrollment::where('user_id', $r->user_id)->where('status', 'completed')->count(),
                ]);

            // Highest completion
            $highestCompletion = Enrollment::selectRaw('user_id, COUNT(*) as total, SUM(CASE WHEN status=\'completed\' THEN 1 ELSE 0 END) as completed')
                ->groupBy('user_id')->havingRaw('COUNT(*) >= 2')
                ->orderByRaw('SUM(CASE WHEN status=\'completed\' THEN 1 ELSE 0 END)::float / COUNT(*) DESC')
                ->take(10)->with('user:id,name')->get()
                ->map(fn ($r) => [
                    'name'      => $r->user?->name ?? '—',
                    'rate'      => $r->total > 0 ? round($r->completed / $r->total * 100, 1) : 0,
                    'completed' => (int) $r->completed,
                    'enrolled'  => (int) $r->total,
                ]);

            return [
                'kpis' => [
                    'totalStudents'     => $totalStudents,
                    'newStudents'       => $newStudents,
                    'activeLearners'    => $activeLearners,
                    'activeStudents'    => $activeCount,
                    'dormantStudents'   => $dormantCount,
                    'returningStudents' => $returningStudents,
                    'completionRate'    => $completionRate,
                    'avgLearningHours'  => $avgWatchHours,
                    'dropoutRate'       => $dropoutRate,
                ],
                'growthLabels'      => $growthLabels,
                'growthValues'      => $growthValues,
                'hoursLabels'       => $hoursLabels,
                'hoursValues'       => $hoursValues,
                'mostActive'        => $mostActive,
                'highestCompletion' => $highestCompletion,
            ];
        });
    }
}
