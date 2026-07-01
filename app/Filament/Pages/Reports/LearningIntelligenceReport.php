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
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 7;
    protected static ?string $slug = 'reports/learning-intelligence';

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

        $header = ['Course', 'Enrollments', 'Avg Progress %', 'Completion Rate %', 'Certificate Rate %', 'Total Watch Hours', 'Avg Watch Hours/Learner'];

        $rows = $data['courses']->map(fn (array $c) => [
            $c['title'],
            $c['enrollments'],
            $c['avgProgress'],
            $c['completionRate'],
            $c['certificateRate'],
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

        return array_merge(
            ['activePreset' => $filters['preset']],
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
            'preset' => request('preset', 'this_month'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
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
        $certificatesIssued = (clone $enrollmentsQuery)->where('certificate_issued', true)->count();

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
        $certificateRate = $completedEnrollments > 0 ? round(($certificatesIssued / $completedEnrollments) * 100, 1) : 0;

        $courses = Course::withoutGlobalScopes()
            ->get(['id', 'title'])
            ->map(function (Course $course) use ($from, $to) {
                $courseEnrollments = Enrollment::where('course_id', $course->id);
                static::applyDateRange($courseEnrollments, 'enrolled_at', $from, $to);
                $enrollCount = $courseEnrollments->count();
                $completedCount = (clone $courseEnrollments)->whereNotNull('completed_at')->count();
                $certCount = (clone $courseEnrollments)->where('certificate_issued', true)->count();
                $avgProgress = (float) (clone $courseEnrollments)->avg('progress_percentage');

                $watchSeconds = (float) LessonProgress::where('course_id', $course->id)->sum('watch_time');
                $learnerCount = LessonProgress::where('course_id', $course->id)->distinct('user_id')->count('user_id');

                return [
                    'title' => $course->title,
                    'enrollments' => $enrollCount,
                    'avgProgress' => round($avgProgress, 1),
                    'completionRate' => $enrollCount > 0 ? round(($completedCount / $enrollCount) * 100, 1) : 0,
                    'certificateRate' => $completedCount > 0 ? round(($certCount / $completedCount) * 100, 1) : 0,
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
                'certificateRate' => $certificateRate,
            ],
            'courses' => $courses,
        ];
    }
}
