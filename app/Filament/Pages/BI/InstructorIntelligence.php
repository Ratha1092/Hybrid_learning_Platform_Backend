<?php

namespace App\Filament\Pages\BI;

use App\Domains\Courses\Models\Course;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasBiCsvExport;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class InstructorIntelligence extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.instructor-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Instructor Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'bi/instructors';

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
        return PanelAccess::can('bi.view_instructors');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.instructor-intelligence';
    }

    public static function pdfViewData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Instructor Intelligence Report',
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

        $cacheKey = 'bi.instructors.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            $totalInstructors = User::role('instructor')->count();
            $avgRating        = round((float) Review::avg('rating'), 2);

            $paidOrderIds = Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->when($from, fn ($q) => static::applyDateRange($q, 'paid_at', $from, $to))
                ->pluck('id');

            $totalEarnings = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('instructor_amount');
            $avgEarnings   = $totalInstructors > 0 ? $totalEarnings / $totalInstructors : 0.0;
            $pendingPayout = (float) InstructorWallet::sum('pending_balance');

            // Period new courses
            $newCourses = (int) static::applyDateRange(Course::query(), 'created_at', $from, $to)->count();

            // Top instructors by period earnings — single grouped query (no N+1)
            $earningsRows = OrderItem::whereIn('order_id', $paidOrderIds)
                ->groupBy('instructor_id')
                ->selectRaw('instructor_id, SUM(instructor_amount) as earnings, COUNT(*) as sales')
                ->orderByRaw('SUM(instructor_amount) DESC')
                ->take(10)
                ->get();

            $topIds    = $earningsRows->pluck('instructor_id');
            $iUsers    = User::whereIn('id', $topIds)->with('wallet')->get()->keyBy('id');
            $iRatings  = Review::whereHas('course', fn ($q) => $q->whereIn('instructor_id', $topIds))
                ->join('courses', 'reviews.course_id', '=', 'courses.id')
                ->selectRaw('courses.instructor_id, AVG(reviews.rating) as avg_rating')
                ->groupBy('courses.instructor_id')
                ->pluck('avg_rating', 'instructor_id');
            $iCourses  = Course::whereIn('instructor_id', $topIds)
                ->selectRaw('instructor_id, COUNT(*) as cnt')
                ->groupBy('instructor_id')
                ->pluck('cnt', 'instructor_id');
            $iStudents = Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->whereIn('courses.instructor_id', $topIds)
                ->selectRaw('courses.instructor_id, COUNT(DISTINCT enrollments.user_id) as students')
                ->groupBy('courses.instructor_id')
                ->pluck('students', 'instructor_id');

            $topInstructors = $earningsRows->map(fn ($r) => [
                'name'        => $iUsers[$r->instructor_id]?->name ?? 'Unknown',
                'earnings'    => (float) $r->earnings,
                'sales'       => (int) $r->sales,
                'courses'     => (int) ($iCourses[$r->instructor_id] ?? 0),
                'students'    => (int) ($iStudents[$r->instructor_id] ?? 0),
                'avg_rating'  => round((float) ($iRatings[$r->instructor_id] ?? 0), 1),
                'wallet_balance' => (float) ($iUsers[$r->instructor_id]?->wallet?->balance ?? 0),
            ]);

            // Top rated instructors
            $topRatedRows = Review::join('courses', 'reviews.course_id', '=', 'courses.id')
                ->selectRaw('courses.instructor_id, AVG(reviews.rating) as avg_rating, COUNT(*) as reviews')
                ->groupBy('courses.instructor_id')
                ->havingRaw('COUNT(*) >= 5')
                ->orderByRaw('AVG(reviews.rating) DESC')
                ->take(10)
                ->get();
            $ratedIds   = $topRatedRows->pluck('instructor_id');
            $ratedNames = User::whereIn('id', $ratedIds)->pluck('name', 'id');
            $topRated   = $topRatedRows->map(fn ($r) => [
                'name'       => $ratedNames[$r->instructor_id] ?? 'Unknown',
                'avg_rating' => round((float) $r->avg_rating, 2),
                'reviews'    => (int) $r->reviews,
            ]);

            // Instructor growth (6 months)
            $growthLabels = [];
            $growthValues = [];
            for ($i = 5; $i >= 0; $i--) {
                $m    = now()->subMonths($i)->startOfMonth();
                $mEnd = (clone $m)->endOfMonth();
                $growthLabels[] = $m->format("M 'y");
                $growthValues[] = User::role('instructor')->whereBetween('created_at', [$m, $mEnd])->count();
            }

            // Top 10 earnings for chart
            $earningsChartLabels = $topInstructors->take(8)->pluck('name')->map(fn ($n) => strlen($n) > 20 ? substr($n, 0, 18).'…' : $n)->toArray();
            $earningsChartValues = $topInstructors->take(8)->pluck('earnings')->toArray();

            // Rating distribution
            $ratingDist = Review::selectRaw('rating, COUNT(*) as cnt')->groupBy('rating')->orderBy('rating')->pluck('cnt', 'rating');
            $ratingDistLabels = ['1★', '2★', '3★', '4★', '5★'];
            $ratingDistValues = array_map(fn ($r) => (int) ($ratingDist[$r] ?? 0), [1, 2, 3, 4, 5]);

            return [
                'kpis' => [
                    'totalInstructors' => $totalInstructors,
                    'totalEarnings'    => $totalEarnings,
                    'avgEarnings'      => $avgEarnings,
                    'avgRating'        => $avgRating,
                    'newCourses'       => $newCourses,
                    'pendingPayout'    => $pendingPayout,
                ],
                'topInstructors'       => $topInstructors,
                'topRated'             => $topRated,
                'growthLabels'         => $growthLabels,
                'growthValues'         => $growthValues,
                'earningsChartLabels'  => $earningsChartLabels,
                'earningsChartValues'  => $earningsChartValues,
                'ratingDistLabels'     => $ratingDistLabels,
                'ratingDistValues'     => $ratingDistValues,
            ];
        });
    }
}
