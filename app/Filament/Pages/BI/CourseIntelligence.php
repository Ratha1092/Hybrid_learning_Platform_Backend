<?php

namespace App\Filament\Pages\BI;

use App\Domains\Analytics\Models\CourseView;
use App\Domains\Courses\Models\Course;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Orders\Models\Refund;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class CourseIntelligence extends Page
{
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.course-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Course Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 6;
    protected static ?string $slug = 'bi/courses';

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
        return PanelAccess::can('bi.view_courses');
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

        $cacheKey = 'bi.courses.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            // Status counts (all-time)
            $published  = Course::where('is_published', true)->count();
            $draft      = Course::where('status', Course::STATUS_DRAFT)->count();
            $pending    = Course::where('status', Course::STATUS_PENDING)->count();
            $archived   = Course::where('status', 'archived')->count();

            // Period metrics
            $periodViews       = (int) static::applyDateRange(CourseView::query(), 'created_at', $from, $to)->count();
            $periodEnrollments = (int) static::applyDateRange(Enrollment::query(), 'created_at', $from, $to)->count();
            $periodWishlists   = (int) static::applyDateRange(Wishlist::query(), 'created_at', $from, $to)->count();
            $avgRating         = round((float) Review::avg('rating'), 2);
            $totalEnrollments  = Enrollment::count();
            $completedEnrollments = Enrollment::where('status', 'completed')->count();
            $completionRate    = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0.0;

            // Enrollment trend (12 months)
            $eTrend = [];
            for ($i = 11; $i >= 0; $i--) {
                $m    = now()->subMonths($i)->startOfMonth();
                $mEnd = (clone $m)->endOfMonth();
                $eTrend[] = ['label' => $m->format("M 'y"), 'count' => Enrollment::whereBetween('created_at', [$m, $mEnd])->count()];
            }

            // Rating trend (6 months avg)
            $rTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $m    = now()->subMonths($i)->startOfMonth();
                $mEnd = (clone $m)->endOfMonth();
                $rTrend[] = ['label' => $m->format("M 'y"), 'avg' => round((float) Review::whereBetween('created_at', [$m, $mEnd])->avg('rating'), 2)];
            }

            // Status breakdown for donut
            $statusBreakdown = [
                'Published' => $published,
                'Draft'     => $draft,
                'Pending'   => $pending,
                'Archived'  => $archived,
            ];

            // Top courses by revenue (from order_items)
            $topRevenueCourses = OrderItem::selectRaw('course_id, SUM(final_amount) as total_revenue, COUNT(*) as total_sales')
                ->groupBy('course_id')
                ->orderByRaw('SUM(final_amount) DESC')
                ->take(10)
                ->with('course:id,title')
                ->get();

            // Highest rated
            $highestRated = Review::selectRaw('course_id, AVG(rating) as avg_rating, COUNT(*) as review_count')
                ->groupBy('course_id')
                ->havingRaw('COUNT(*) >= 3')
                ->orderByRaw('AVG(rating) DESC')
                ->take(10)
                ->with('course:id,title')
                ->get();

            // Most viewed (period)
            $mostViewed = static::applyDateRange(
                CourseView::selectRaw('course_id, COUNT(*) as views'), 'created_at', $from, $to
            )->groupBy('course_id')->orderByRaw('COUNT(*) DESC')->take(10)
                ->with('course:id,title')->get();

            // Lowest completion rate (min 10 enrollments)
            $lowestCompletion = Enrollment::selectRaw('course_id, COUNT(*) as total, SUM(CASE WHEN status=\'completed\' THEN 1 ELSE 0 END) as completed')
                ->groupBy('course_id')
                ->havingRaw('COUNT(*) >= 10')
                ->orderByRaw('SUM(CASE WHEN status=\'completed\' THEN 1 ELSE 0 END)::float / COUNT(*) ASC')
                ->take(10)
                ->with('course:id,title')
                ->get()
                ->map(fn ($r) => ['title' => $r->course?->title ?? '—', 'rate' => $r->total > 0 ? round($r->completed / $r->total * 100, 1) : 0, 'enrollments' => $r->total]);

            return [
                'kpis' => [
                    'published'       => $published,
                    'draft'           => $draft,
                    'pending'         => $pending,
                    'archived'        => $archived,
                    'periodViews'     => $periodViews,
                    'periodEnrollments' => $periodEnrollments,
                    'periodWishlists' => $periodWishlists,
                    'avgRating'       => $avgRating,
                    'completionRate'  => $completionRate,
                ],
                'enrollmentTrend'  => $eTrend,
                'ratingTrend'      => $rTrend,
                'statusBreakdown'  => $statusBreakdown,
                'topRevenueCourses'=> $topRevenueCourses,
                'highestRated'     => $highestRated,
                'mostViewed'       => $mostViewed,
                'lowestCompletion' => $lowestCompletion,
            ];
        });
    }
}
