<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class PayoutReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.payout-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';
    protected static ?string $navigationLabel = 'Payouts Report';
    protected static string|\UnitEnum|null $navigationGroup = 'Data Exports';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'reports/payouts';

    public string $preset = 'this_month';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $status = 'all';
    public int $page = 1;

    public function mount(): void
    {
        $this->preset   = request('preset', 'this_month');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');
        $this->status   = request('status', 'all');
        $this->page     = (int) request('page', 1);
    }

    public function applyDateFilter(string $preset, string $from = '', string $to = ''): void
    {
        $this->preset   = $preset ?: 'this_month';
        $this->dateFrom = $preset === 'custom' ? $from : '';
        $this->dateTo   = $preset === 'custom' ? $to : '';
        $this->page     = 1;
        $this->dispatchDateResolved();
    }

    public function updatedStatus(): void { $this->page = 1; }

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_payout');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Schedulable contract

    public static function reportKey(): string
    {
        return 'payout';
    }

    public static function reportLabel(): string
    {
        return 'Payout';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.payout-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        $summary = static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);

        if (($filters['status'] ?? 'all') !== 'all') {
            $summary .= ' · ' . ucfirst($filters['status']);
        }

        return $summary;
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        $header = ['Instructor', 'Amount', 'Currency', 'Payment Method', 'Status', 'Requested At', 'Processed At', 'Rejection Reason'];

        $rows = $data['payouts']->map(fn (PayoutRequest $p) => [
            $p->instructor?->name ?? '',
            number_format((float) $p->amount, 2),
            $p->currency,
            $p->payment_method,
            $p->status,
            $p->created_at?->format('M d, Y H:i') ?? '',
            $p->processed_at?->format('M d, Y H:i') ?? '',
            $p->rejection_reason ?? '',
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'Payout Report',
            'filtersSummary' => static::filtersSummary($filters),
            'kpis' => $data['kpis'],
            'statusBreakdown' => $data['statusBreakdown'],
            'payouts' => $data['payouts']->take(200),
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
                'activeStatus'   => $filters['status'],
            ],
            static::buildReportData($filters, paginate: true),
        );
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'payout-report-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // Shared cor

    private function currentFilters(): array
    {
        return [
            'preset'    => $this->preset ?: 'this_month',
            'date_from' => $this->dateFrom ?: null,
            'date_to'   => $this->dateTo ?: null,
            'status'    => $this->status,
            'page'      => $this->page,
            'per_page'  => 10,
        ];
    }

    public static function buildReportData(array $filters, bool $paginate = true): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
        $status = $filters['status'] ?? 'all';

        $base = function () use ($from, $to, $status) {
            $q = PayoutRequest::query();
            static::applyDateRange($q, 'created_at', $from, $to);
            if ($status !== 'all') {
                $q->where('status', $status);
            }
            return $q;
        };

        $requestedAmount = (clone $base())->sum('amount');
        $approvedAmount = (clone $base())->where('status', 'approved')->sum('amount');
        $pendingAmount = (clone $base())->where('status', 'pending')->sum('amount');
        $rejectedAmount = (clone $base())->where('status', 'rejected')->sum('amount');

        $avgProcessingHours = (clone $base())
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('processed_at')
            ->get(['created_at', 'processed_at'])
            ->avg(fn (PayoutRequest $p) => $p->created_at->diffInHours($p->processed_at));

        $totalOutstandingBalance = InstructorWallet::sum('balance');

        $statusBreakdown = collect(['pending', 'approved', 'rejected'])->mapWithKeys(function (string $st) use ($base) {
            $q = (clone $base())->where('status', $st);
            return [$st => ['count' => $q->count(), 'amount' => (clone $q)->sum('amount')]];
        })->all();

        $query = $base()->with(['instructor:id,name,email'])->orderByDesc('id');

        if ($paginate) {
            $page = max(1, (int) ($filters['page'] ?? 1));
            $perPage = in_array((int) ($filters['per_page'] ?? 10), [10, 25, 50]) ? (int) $filters['per_page'] : 10;
            $totalRows = (clone $query)->count();
            $totalPages = max(1, (int) ceil($totalRows / $perPage));
            $payouts = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        } else {
            $payouts = $query->get();
            $totalRows = $payouts->count();
            $totalPages = 1;
            $page = 1;
            $perPage = $totalRows;
        }

        return [
            'kpis' => [
                'requestedAmount' => $requestedAmount,
                'approvedAmount' => $approvedAmount,
                'pendingAmount' => $pendingAmount,
                'rejectedAmount' => $rejectedAmount,
                'avgProcessingHours' => round((float) ($avgProcessingHours ?? 0), 1),
                'totalOutstandingBalance' => $totalOutstandingBalance,
            ],
            'statusBreakdown' => $statusBreakdown,
            'payouts' => $payouts,
            'total' => $totalRows,
            'totalPages' => $totalPages,
            'curPage' => $page,
            'perPage' => $perPage,
        ];
    }
}
