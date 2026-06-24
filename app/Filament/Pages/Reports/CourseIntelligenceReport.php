<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Analytics\Models\CourseView;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class CourseIntelligenceReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.course-intelligence-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Course Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'reports/course-intelligence';

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_course_intelligence');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Schedulable contract

    public static function reportKey(): string
    {
        return 'course_intelligence';
    }

    public static function reportLabel(): string
    {
        return 'Course Intelligence';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.course-intelligence-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        return static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters);

        $header = ['Course', 'Instructor', 'Category', 'Views', 'Enrollments', 'Conversion %', 'Avg Rating', 'Completion %', 'Certificates'];

        $rows = $data['courses']->map(fn (array $c) => [
            $c['title'],
            $c['instructor'],
            $c['category'],
            $c['views'],
            $c['enrollments'],
            $c['conversionRate'],
            $c['avgRating'],
            $c['completionRate'],
            $c['certificatesIssued'],
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Course Intelligence Report',
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
            filename: 'course-intelligence-report-' . now()->format('Y-m-d') . '.csv',
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

        $viewsQuery = CourseView::query();
        static::applyDateRange($viewsQuery, 'created_at', $from, $to);
        $totalViews = (clone $viewsQuery)->count();
        $uniqueViewers = (clone $viewsQuery)->distinct('user_id')->whereNotNull('user_id')->count('user_id');

        $enrollmentsQuery = Enrollment::query();
        static::applyDateRange($enrollmentsQuery, 'enrolled_at', $from, $to);
        $totalEnrollments = (clone $enrollmentsQuery)->count();
        $completedEnrollments = (clone $enrollmentsQuery)->whereNotNull('completed_at')->count();
        $certificatesIssued = (clone $enrollmentsQuery)->where('certificate_issued', true)->count();

        $conversionRate = $totalViews > 0 ? round(($totalEnrollments / $totalViews) * 100, 1) : 0;
        $completionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;
        $certificateRate = $completedEnrollments > 0 ? round(($certificatesIssued / $completedEnrollments) * 100, 1) : 0;
        $avgRating = round((float) \App\Domains\Learning\Models\Review::avg('rating'), 2);

        $courses = Course::withoutGlobalScopes()
            ->with(['instructor:id,name', 'category:id,name'])
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->get()
            ->map(function (Course $course) use ($from, $to) {
                $views = CourseView::where('course_id', $course->id);
                static::applyDateRange($views, 'created_at', $from, $to);
                $viewCount = $views->count();

                $courseEnrollments = Enrollment::where('course_id', $course->id);
                static::applyDateRange($courseEnrollments, 'enrolled_at', $from, $to);
                $enrollCount = $courseEnrollments->count();
                $completedCount = (clone $courseEnrollments)->whereNotNull('completed_at')->count();
                $certCount = (clone $courseEnrollments)->where('certificate_issued', true)->count();

                return [
                    'title' => $course->title,
                    'instructor' => $course->instructor?->name ?? '—',
                    'category' => $course->category?->name ?? '—',
                    'views' => $viewCount,
                    'enrollments' => $enrollCount,
                    'conversionRate' => $viewCount > 0 ? round(($enrollCount / $viewCount) * 100, 1) : 0,
                    'avgRating' => round((float) ($course->reviews_avg_rating ?? 0), 2),
                    'completionRate' => $enrollCount > 0 ? round(($completedCount / $enrollCount) * 100, 1) : 0,
                    'certificatesIssued' => $certCount,
                ];
            })
            ->sortByDesc('views')
            ->values();

        return [
            'kpis' => [
                'totalViews' => $totalViews,
                'uniqueViewers' => $uniqueViewers,
                'totalEnrollments' => $totalEnrollments,
                'conversionRate' => $conversionRate,
                'avgRating' => $avgRating,
                'completionRate' => $completionRate,
                'certificateRate' => $certificateRate,
            ],
            'courses' => $courses,
        ];
    }
}
