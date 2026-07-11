<?php

namespace App\Filament\Pages\BI;

use App\Domains\Analytics\Models\CourseView;
use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Wishlist;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Orders\Models\Refund;
use App\Domains\Promotions\Models\Coupon;
use App\Support\Concerns\HasBiCsvExport;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class MarketplaceIntelligence extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.marketplace-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Marketplace Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'bi/marketplace';

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
        return PanelAccess::can('bi.view_marketplace');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.marketplace-intelligence';
    }

    public static function pdfViewData(array $filters): array
    {
        $preset = $filters['preset'] ?? 'this_month';

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Marketplace Intelligence Report',
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

        $cacheKey = 'bi.marketplace.' . md5(serialize([$preset, $from?->toDateString(), $to?->toDateString()]));

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            $totalViews      = (int) static::applyDateRange(CourseView::query(), 'created_at', $from, $to)->count();
            $uniqueViewers   = (int) static::applyDateRange(CourseView::query()->selectRaw('COUNT(DISTINCT user_id) as cnt'), 'created_at', $from, $to)->value('cnt') ?? 0;
            $totalWishlists  = (int) static::applyDateRange(Wishlist::query(), 'created_at', $from, $to)->count();
            $totalOrders     = (int) static::applyDateRange(
                Order::query()->where('payment_status', OrderPaymentStatus::Paid->value), 'paid_at', $from, $to
            )->count();
            $totalEnrollments = (int) static::applyDateRange(Enrollment::query(), 'created_at', $from, $to)->count();

            $paidOrderIds = Order::where('payment_status', OrderPaymentStatus::Paid->value)
                ->when($from, fn ($q) => static::applyDateRange($q, 'paid_at', $from, $to))
                ->pluck('id');
            $gmv              = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('final_amount');
            $platformCommission = (float) OrderItem::whereIn('order_id', $paidOrderIds)->sum('platform_amount');
            $avgCoursePrice   = round((float) Course::where('is_published', true)->avg('price'), 2);

            $publishedCount   = max(1, Course::where('is_published', true)->count());
            $totalSales       = OrderItem::count();
            $avgRevenuePerCourse = $publishedCount > 0 ? round((float) OrderItem::sum('final_amount') / $publishedCount, 2) : 0.0;

            $refundCount      = (int) static::applyDateRange(Refund::query(), 'created_at', $from, $to)->count();
            $refundRate       = $totalOrders > 0 ? round($refundCount / $totalOrders * 100, 1) : 0.0;
            $conversionRate   = $uniqueViewers > 0 ? round($totalOrders / $uniqueViewers * 100, 1) : 0.0;

            // Coupon usage
            $couponUsage      = Coupon::where('used_count', '>', 0)->orderByDesc('used_count')->take(10)->get();
            $totalCouponUses  = (int) Coupon::sum('used_count');
            $couponRate       = $totalOrders > 0 ? round($totalCouponUses / max($totalOrders + $totalCouponUses, 1) * 100, 1) : 0.0;

            // Funnel data as indexed array for blade iteration
            $completedCount = (int) Enrollment::where('status', 'completed')
                ->when($from, fn ($q) => static::applyDateRange($q, 'created_at', $from, $to))->count();
            $funnel = [
                ['label' => 'Total Views',    'value' => $totalViews],
                ['label' => 'Unique Viewers', 'value' => $uniqueViewers],
                ['label' => 'Wishlisted',     'value' => $totalWishlists],
                ['label' => 'Orders Placed',  'value' => $totalOrders],
                ['label' => 'Enrolled',       'value' => $totalEnrollments],
                ['label' => 'Completed',      'value' => $completedCount],
            ];

            // Category enrollments (top 8)
            $categoryEnrollments = Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->join('categories', 'courses.category_id', '=', 'categories.id')
                ->when($from, fn ($q) => static::applyDateRange($q, 'enrollments.created_at', $from, $to))
                ->selectRaw('categories.name as name, COUNT(*) as count')
                ->groupBy('categories.name')
                ->orderByRaw('COUNT(*) DESC')
                ->take(8)
                ->get();

            // Most wishlisted
            $mostWishlisted = Wishlist::selectRaw('course_id, COUNT(*) as wish_count')
                ->groupBy('course_id')->orderByRaw('COUNT(*) DESC')->take(10)
                ->with('course:id,title,price')->get();

            // Highest conversion (min 20 views)
            $hcRaw = CourseView::selectRaw('course_id, COUNT(*) as views')
                ->groupBy('course_id')->havingRaw('COUNT(*) >= 20')
                ->get()->map(function ($r) {
                    $orders = Enrollment::where('course_id', $r->course_id)->count();
                    return ['course_id' => $r->course_id, 'views' => $r->views, 'orders' => $orders, 'rate' => $r->views > 0 ? round($orders / $r->views * 100, 1) : 0];
                })->sortByDesc('rate')->take(10)->values();
            $hcCourseTitles = Course::whereIn('id', $hcRaw->pluck('course_id'))->pluck('title', 'id');
            $highestConversion = $hcRaw->map(fn ($r) => array_merge($r, ['title' => $hcCourseTitles[$r['course_id']] ?? '—']))->values();

            return [
                'kpis' => [
                    'totalViews'          => $totalViews,
                    'uniqueViewers'       => $uniqueViewers,
                    'wishlistCount'       => $totalWishlists,
                    'totalOrders'         => $totalOrders,
                    'totalEnrollments'    => $totalEnrollments,
                    'gmv'                 => $gmv,
                    'platformRevenue'     => $platformCommission,
                    'avgPrice'            => $avgCoursePrice,
                    'avgRevPerCourse'     => $avgRevenuePerCourse,
                    'refundRate'          => $refundRate,
                    'conversionRate'      => $conversionRate,
                    'couponUsageRate'     => $couponRate,
                ],
                'funnel'              => $funnel,
                'categoryEnrollments' => $categoryEnrollments,
                'mostWishlisted'      => $mostWishlisted,
                'highestConversion'   => $highestConversion,
                'activeCoupons'       => $couponUsage,
            ];
        });
    }
}
