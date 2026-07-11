<?php

namespace App\Filament\Pages\BI;

use App\Domains\Courses\Models\Course;
use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\Refund;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Users\Models\InstructorVerification;
use App\Domains\Learning\Models\Review;
use App\Support\Concerns\HasBiCsvExport;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class OperationalIntelligence extends Page
{
    use HasBiCsvExport;
    use HasDateRangePresets;

    protected string $view = 'filament.pages.bi.operational-intelligence';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Operational Intelligence';
    protected static string|\UnitEnum|null $navigationGroup = 'Business Intelligence';
    protected static ?int $navigationSort = 8;
    protected static ?string $slug = 'bi/operations';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable { return ''; }

    public static function canAccess(): bool
    {
        return PanelAccess::can('bi.view_operations');
    }

    public static function pdfView(): string
    {
        return 'bi.pdf.operational-intelligence';
    }

    public static function pdfViewData(array $filters): array
    {
        $data = (new static())->getViewData();

        return array_merge([
            'siteName'       => \App\Domains\System\Models\Setting::get('site_name', config('app.name')),
            'title'          => 'Operational Intelligence Report',
            'filtersSummary' => $data['periodLabel'] ?? '',
        ], $data);
    }

    public function getViewData(): array
    {
        $preset = request('preset', 'this_month');
        [$from, $to] = static::resolvePreset($preset, 'this_month', request('date_from'), request('date_to'));

        $fromKey  = $from ? $from->format('Ymd') : 'null';
        $toKey    = $to   ? $to->format('Ymd')   : 'null';
        $cacheKey = "bi.operations.{$preset}.{$fromKey}.{$toKey}";

        $data = Cache::remember($cacheKey, 60, function () use ($from, $to) {
            // KPIs — live system state
            $pendingCourseReviews  = Course::where('status', Course::STATUS_PENDING)->count();
            $pendingVerifications  = InstructorVerification::pending()->count();
            $failedPaymentsToday   = Payment::where('status', PaymentStatus::Failed->value)
                ->whereDate('created_at', today())->count();
            $openRefunds = (int) static::applyDateRange(Refund::query(), 'created_at', $from, $to)->count();
            $failedJobs = DB::table('failed_jobs')->count();
            try {
                $queueJobs = Queue::size();
            } catch (\Throwable $e) {
                $queueJobs = 0;
            }

            // Avg order processing time (selected period, paid orders)
            $avgProcessingMins = round((float) static::applyDateRange(
                Order::where('payment_status', OrderPaymentStatus::Paid->value)->whereNotNull('paid_at'),
                'paid_at', $from, $to
            )->selectRaw("AVG(EXTRACT(EPOCH FROM (paid_at - created_at)) / 60) as avg_mins")
                ->value('avg_mins') ?? 0, 1);

            // Order status breakdown
            $orderStatusBreakdown = [
                'Pending'   => Order::where('payment_status', OrderPaymentStatus::Pending->value)->count(),
                'Completed' => Order::where('payment_status', OrderPaymentStatus::Paid->value)->count(),
                'Failed'    => Order::where('payment_status', OrderPaymentStatus::Failed->value)->count(),
            ];

            // Payment status trend (last 7 days)
            $payTrendLabels   = [];
            $payTrendPaid     = [];
            $payTrendFailed   = [];
            $payTrendPending  = [];
            for ($i = 6; $i >= 0; $i--) {
                $day              = now()->subDays($i)->toDateString();
                $payTrendLabels[] = Carbon::parse($day)->format('M d');
                $payTrendPaid[]   = Payment::whereDate('created_at', $day)->where('status', PaymentStatus::Paid->value)->count();
                $payTrendFailed[] = Payment::whereDate('created_at', $day)->where('status', PaymentStatus::Failed->value)->count();
                $payTrendPending[]= Payment::whereDate('created_at', $day)->where('status', PaymentStatus::Pending->value)->count();
            }

            // Review queue trend (30 days)
            $reviewTrendLabels = [];
            $reviewTrendValues = [];
            for ($i = 29; $i >= 0; $i--) {
                $day               = now()->subDays($i)->toDateString();
                $reviewTrendLabels[] = ($i % 5 === 0) ? Carbon::parse($day)->format('M d') : '';
                $reviewTrendValues[] = Course::where('status', Course::STATUS_PENDING)
                    ->whereDate('updated_at', $day)->count();
            }

            // Tables
            $pendingCourses = Course::where('status', Course::STATUS_PENDING)
                ->with('instructor:id,name')->orderByDesc('updated_at')->take(10)->get();

            $recentRefunds = Refund::with(['order' => fn ($q) => $q->select('id', 'order_number', 'customer_name')])
                ->orderByDesc('created_at')->take(10)->get();

            $failedPayments = static::applyDateRange(
                Payment::where('status', PaymentStatus::Failed->value), 'created_at', $from, $to
            )->with('order:id,order_number')->orderByDesc('created_at')->take(15)->get();

            $verificationQueue = InstructorVerification::pending()
                ->with('user:id,name,email')->orderByDesc('created_at')->take(10)->get();

            $failedJobsList = DB::table('failed_jobs')->orderByDesc('failed_at')->take(5)->get();

            return [
                'kpis' => [
                    'pendingCourseReviews' => $pendingCourseReviews,
                    'pendingVerifications' => $pendingVerifications,
                    'failedPaymentsToday'  => $failedPaymentsToday,
                    'openRefunds'          => $openRefunds,
                    'failedJobs'           => $failedJobs,
                    'queueJobs'            => $queueJobs,
                    'avgProcessingMins'    => $avgProcessingMins,
                ],
                'orderStatusBreakdown' => $orderStatusBreakdown,
                'payTrendLabels'       => $payTrendLabels,
                'payTrendPaid'         => $payTrendPaid,
                'payTrendFailed'       => $payTrendFailed,
                'payTrendPending'      => $payTrendPending,
                'reviewTrendLabels'    => $reviewTrendLabels,
                'reviewTrendValues'    => $reviewTrendValues,
                'pendingCourses'       => $pendingCourses,
                'recentRefunds'        => $recentRefunds,
                'failedPayments'       => $failedPayments,
                'verificationQueue'    => $verificationQueue,
                'failedJobsList'       => $failedJobsList,
            ];
        });

        return array_merge([
            'activePreset'   => $preset,
            'activeDateFrom' => $from?->format('Y-m-d') ?? '',
            'activeDateTo'   => $to?->format('Y-m-d') ?? '',
            'periodLabel'    => static::dateRangePresetOptions()[$preset] ?? 'Selected Period',
        ], $data);
    }
}
