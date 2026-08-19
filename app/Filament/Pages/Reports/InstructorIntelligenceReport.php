<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Courses\Models\Course;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class InstructorIntelligenceReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.instructor-intelligence-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Instructor Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Data Exports';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'reports/instructor-intelligence';

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
        return PanelAccess::can('reports.view_instructor_intelligence');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // ── Schedulable contract ────────────────────────────────────────────

    public static function reportKey(): string
    {
        return 'instructor_intelligence';
    }

    public static function reportLabel(): string
    {
        return 'Instructor Intelligence';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.instructor-intelligence-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        return static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters);

        $header = ['Instructor', 'Courses', 'Students', 'Earnings (Period)', 'Wallet Balance', 'Pending Balance', 'Avg Rating'];

        $rows = $data['instructors']->map(fn (array $i) => [
            $i['name'],
            $i['courseCount'],
            $i['studentCount'],
            number_format($i['earnings'], 2),
            number_format($i['walletBalance'], 2),
            number_format($i['pendingBalance'], 2),
            $i['avgRating'],
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Instructor Intelligence Report',
            'filtersSummary' => static::filtersSummary($filters),
            'kpis' => $data['kpis'],
            'instructors' => $data['instructors']->take(200),
        ];
    }

    // ── Interactive page ────────────────────────────────────────────────

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
            filename: 'instructor-intelligence-report-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // ── Shared core ──────────────────────────────────────────────────────

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

        $instructorIds = User::role('instructor')->pluck('id');
        $totalInstructors = $instructorIds->count();

        $earningsQuery = OrderItem::whereIn('instructor_id', $instructorIds)
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->where('payment_status', OrderPaymentStatus::Paid->value);
                static::applyDateRange($q, 'paid_at', $from, $to);
            });

        $totalEarnings = (clone $earningsQuery)->sum('instructor_amount');
        $averageEarnings = $totalInstructors > 0 ? $totalEarnings / $totalInstructors : 0;
        $averageRating = round((float) Review::avg('rating'), 2);

        // Batched per-instructor aggregates instead of N+1 queries per instructor.
        $earningsByInstructor = (clone $earningsQuery)
            ->selectRaw('instructor_id, SUM(instructor_amount) as earnings')
            ->groupBy('instructor_id')
            ->pluck('earnings', 'instructor_id');

        $courseCountsByInstructor = Course::whereIn('instructor_id', $instructorIds)
            ->selectRaw('instructor_id, COUNT(*) as cnt')
            ->groupBy('instructor_id')
            ->pluck('cnt', 'instructor_id');

        $studentCountsByInstructor = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->whereIn('courses.instructor_id', $instructorIds)
            ->selectRaw('courses.instructor_id as instructor_id, COUNT(DISTINCT enrollments.user_id) as cnt')
            ->groupBy('courses.instructor_id')
            ->pluck('cnt', 'instructor_id');

        $avgRatingByInstructor = Review::query()
            ->join('courses', 'courses.id', '=', 'reviews.course_id')
            ->whereIn('courses.instructor_id', $instructorIds)
            ->selectRaw('courses.instructor_id as instructor_id, AVG(reviews.rating) as avg_rating')
            ->groupBy('courses.instructor_id')
            ->pluck('avg_rating', 'instructor_id');

        $walletsByInstructor = InstructorWallet::whereIn('instructor_id', $instructorIds)
            ->get()
            ->keyBy('instructor_id');

        $instructors = User::role('instructor')
            ->get()
            ->map(function (User $instructor) use (
                $earningsByInstructor, $courseCountsByInstructor,
                $studentCountsByInstructor, $avgRatingByInstructor, $walletsByInstructor
            ) {
                $wallet = $walletsByInstructor->get($instructor->id);

                return [
                    'name' => $instructor->name,
                    'courseCount' => (int) ($courseCountsByInstructor[$instructor->id] ?? 0),
                    'studentCount' => (int) ($studentCountsByInstructor[$instructor->id] ?? 0),
                    'earnings' => (float) ($earningsByInstructor[$instructor->id] ?? 0),
                    'walletBalance' => (float) ($wallet->balance ?? 0),
                    'pendingBalance' => (float) ($wallet->pending_balance ?? 0),
                    'avgRating' => round((float) ($avgRatingByInstructor[$instructor->id] ?? 0), 2),
                ];
            })
            ->sortByDesc('earnings')
            ->values();

        return [
            'kpis' => [
                'totalInstructors' => $totalInstructors,
                'totalEarnings' => $totalEarnings,
                'averageEarnings' => $averageEarnings,
                'averageRating' => $averageRating,
                'topEarner' => $instructors->first()['name'] ?? '—',
            ],
            'instructors' => $instructors,
        ];
    }
}
