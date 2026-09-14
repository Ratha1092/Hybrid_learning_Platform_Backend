<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class LearningIntelligenceReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.learning-intelligence-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Learning Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Data Exports';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'reports/learning-intelligence';

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

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_learning_intelligence');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Schedulable contract

    public static function reportKey(): string
    {
        return 'learning_intelligence';
    }

    public static function reportLabel(): string
    {
        return 'Learning Intelligence';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.learning-intelligence-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        return static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters);

        $header = ['Course', 'Enrollments', 'Avg Progress %', 'Completion Rate %', 'Total Watch Hours', 'Avg Watch Hours/Learner'];

        $rows = $data['courses']->map(fn (array $c) => [
            $c['title'],
            $c['enrollments'],
            $c['avgProgress'],
            $c['completionRate'],
            $c['totalWatchHours'],
            $c['avgWatchHoursPerLearner'],
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Learning Intelligence Report',
            'filtersSummary' => static::filtersSummary($filters),
            'kpis' => $data['kpis'],
            'courses' => $data['courses']->take(200),
        ];
    }

    // Interactive pag

    public function getViewData(): array
    {
        $filters = $this->currentFilters();
        [$from, $to] = static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        return array_merge(
            [
                'activePreset'   => $filters['preset'],
                'activeDateFrom' => $from?->format('Y-m-d') ?? '',
                'activeDateTo'   => $to?->format('Y-m-d') ?? '',
            ],
            static::buildReportData($filters),
        );
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'learning-intelligence-report-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // Shared cor

    private function currentFilters(): array
    {
        return [
            'preset'    => $this->preset ?: 'this_month',
            'date_from' => $this->dateFrom ?: null,
            'date_to'   => $this->dateTo ?: null,
        ];
    }

    public static function buildReportData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $enrollmentsQuery = Enrollment::query();
        static::applyDateRange($enrollmentsQuery, 'enrolled_at', $from, $to);
        $totalEnrollments = (clone $enrollmentsQuery)->count();
        $completedEnrollments = (clone $enrollmentsQuery)->whereNotNull('completed_at')->count();

        $progressQuery = LessonProgress::query();
        static::applyDateRange($progressQuery, 'last_watched_at', $from, $to);
        $totalWatchSeconds = (clone $progressQuery)->sum('watch_time');
        $activeLearners = (clone $progressQuery)->distinct('user_id')->count('user_id');

        $dropoutCutoff = now()->subDays(30);
        $dropoutCount = Enrollment::whereNull('completed_at')
            ->where('enrolled_at', '<', $dropoutCutoff)
            ->whereDoesntHave('user', fn ($q) => $q->whereHas('lessonProgress', fn ($q2) => $q2->where('last_watched_at', '>=', $dropoutCutoff)))
            ->count();
        $activeEnrollments = Enrollment::whereNull('completed_at')->where('enrolled_at', '<', $dropoutCutoff)->count();

        $completionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;
        $dropoutRate = $activeEnrollments > 0 ? round(($dropoutCount / $activeEnrollments) * 100, 1) : 0;

        // Batched per-course aggregates instead of N+1 queries per course.
        $enrollmentsByCourse = static::applyDateRange(Enrollment::query(), 'enrolled_at', $from, $to)
            ->selectRaw('course_id, COUNT(*) as total, SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed, AVG(progress_percentage) as avg_progress')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $watchByCourse = LessonProgress::query()
            ->selectRaw('course_id, SUM(watch_time) as watch_seconds, COUNT(DISTINCT user_id) as learners')
            ->groupBy('course_id')
            ->get()
            ->keyBy('course_id');

        $courses = Course::query()
            ->get(['id', 'title'])
            ->map(function (Course $course) use ($enrollmentsByCourse, $watchByCourse) {
                $eRow = $enrollmentsByCourse->get($course->id);
                $enrollCount = (int) ($eRow->total ?? 0);
                $completedCount = (int) ($eRow->completed ?? 0);
                $avgProgress = (float) ($eRow->avg_progress ?? 0);

                $wRow = $watchByCourse->get($course->id);
                $watchSeconds = (float) ($wRow->watch_seconds ?? 0);
                $learnerCount = (int) ($wRow->learners ?? 0);

                return [
                    'title' => $course->title,
                    'enrollments' => $enrollCount,
                    'avgProgress' => round($avgProgress, 1),
                    'completionRate' => $enrollCount > 0 ? round(($completedCount / $enrollCount) * 100, 1) : 0,
                    'totalWatchHours' => round($watchSeconds / 3600, 1),
                    'avgWatchHoursPerLearner' => $learnerCount > 0 ? round(($watchSeconds / 3600) / $learnerCount, 1) : 0,
                ];
            })
            ->sortByDesc('enrollments')
            ->values();

        return [
            'kpis' => [
                'totalLearningHours' => round($totalWatchSeconds / 3600, 1),
                'activeLearners' => $activeLearners,
                'completionRate' => $completionRate,
                'dropoutRate' => $dropoutRate,
            ],
            'courses' => $courses,
        ];
    }
}
