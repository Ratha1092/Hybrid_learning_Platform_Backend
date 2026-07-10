<?php

namespace App\Filament\Pages\Reports;

use App\Domains\Learning\Models\Enrollment;
use App\Domains\Orders\Models\Order;
use App\Domains\Reports\Concerns\HasScheduleAction;
use App\Domains\Reports\Contracts\Schedulable;
use App\Domains\System\Models\Setting;
use App\Domains\Users\Models\User;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class UserReport extends Page implements Schedulable
{
    use HasDateRangePresets;
    use HasScheduleAction;

    protected string $view = 'filament.pages.reports.user-report';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'User Report';
    protected static string|\UnitEnum|null $navigationGroup = 'Data Exports';
    protected static ?int $navigationSort = 7;
    protected static ?string $slug = 'reports/users';

    public string $preset = 'this_month';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $role = 'all';
    public string $status = 'all';
    public int $page = 1;

    public function mount(): void
    {
        $this->preset   = request('preset', 'this_month');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');
        $this->role     = request('role', 'all');
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

    public function updatedRole(): void   { $this->page = 1; }
    public function updatedStatus(): void { $this->page = 1; }

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_user');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Schedulable contract

    public static function reportKey(): string
    {
        return 'user';
    }

    public static function reportLabel(): string
    {
        return 'User';
    }

    public static function pdfView(): string
    {
        return 'reports.pdf.user-report';
    }

    public static function filtersSummary(array $filters): string
    {
        $preset = $filters['preset'] ?? 'this_month';
        $summary = static::dateRangePresetOptions()[$preset] ?? ucfirst($preset);

        if (($filters['role'] ?? 'all') !== 'all') {
            $summary .= ' · ' . ucfirst($filters['role']);
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $summary .= ' · ' . ucfirst($filters['status']);
        }

        return $summary;
    }

    public static function csvHeaderAndRows(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        $header = ['Name', 'Email', 'Role', 'Status', 'Enrollments', 'Orders', 'Last Login', 'Joined'];

        $rows = $data['users']->map(fn (User $u) => [
            $u->name,
            $u->email,
            $u->roles->pluck('name')->implode(', '),
            $u->status,
            $u->enrollments_count,
            $u->orders_count,
            $u->last_login_at?->format('M d, Y H:i') ?? '—',
            $u->created_at->format('M d, Y'),
        ])->all();

        return [$header, $rows];
    }

    public static function pdfViewData(array $filters): array
    {
        $data = static::buildReportData($filters, paginate: false);

        return [
            'siteName' => Setting::get('site_name', config('app.name')),
            'title' => 'User Report',
            'filtersSummary' => static::filtersSummary($filters),
            'kpis' => $data['kpis'],
            'roleBreakdown' => $data['roleBreakdown'],
            'statusBreakdown' => $data['statusBreakdown'],
            'users' => $data['users']->take(200),
        ];
    }

    // Interactive page

    public function getViewData(): array
    {
        $filters = $this->currentFilters();

        return array_merge(
            $this->staticFilterMeta($filters),
            static::buildReportData($filters, paginate: true),
        );
    }

    public function exportCsv(): void
    {
        [$header, $rows] = static::csvHeaderAndRows($this->currentFilters());

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'user-report-' . now()->format('Y-m-d') . '.csv',
        );
    }

    // Shared core

    private function currentFilters(): array
    {
        return [
            'preset'    => $this->preset ?: 'this_month',
            'date_from' => $this->dateFrom ?: null,
            'date_to'   => $this->dateTo ?: null,
            'role'      => $this->role,
            'status'    => $this->status,
            'page'      => $this->page,
            'per_page'  => 10,
        ];
    }

    private function staticFilterMeta(array $filters): array
    {
        [$from, $to] = static::resolvePreset($filters['preset'], 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);
        return [
            'activePreset'   => $filters['preset'],
            'activeDateFrom' => $from?->format('Y-m-d') ?? '',
            'activeDateTo'   => $to?->format('Y-m-d') ?? '',
            'activeRole'     => $filters['role'],
            'activeStatus'   => $filters['status'],
        ];
    }

    public static function buildReportData(array $filters, bool $paginate = true): array
    {
        $preset = $filters['preset'] ?? 'this_month';
        [$from, $to] = static::resolvePreset($preset, 'this_month', $filters['date_from'] ?? null, $filters['date_to'] ?? null);

        $role   = $filters['role']   ?? 'all';
        $status = $filters['status'] ?? 'all';

        $base = function () use ($from, $to, $role, $status) {
            $q = User::query()->withCount(['enrollments', 'orders']);
            static::applyDateRange($q, 'created_at', $from, $to);
            if ($role !== 'all') {
                $q->role($role);
            }
            if ($status !== 'all') {
                $q->where('status', $status);
            }
            return $q;
        };

        $totalNew      = $base()->count();
        $activeCount   = (clone $base())->where('status', User::STATUS_ACTIVE)->count();
        $suspendedCount = (clone $base())->where('status', User::STATUS_SUSPENDED)->count();
        $verifiedCount = (clone $base())->whereNotNull('email_verified_at')->count();
        $newEnrollments = static::applyDateRange(Enrollment::query(), 'created_at', $from, $to)->count();

        $roleBreakdown = collect(['student', 'instructor', 'admin', 'super-admin', 'finance-manager', 'moderator'])
            ->mapWithKeys(function (string $r) use ($from, $to, $status) {
                $q = User::role($r);
                static::applyDateRange($q, 'created_at', $from, $to);
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
                return [$r => $q->count()];
            })->all();

        $statusBreakdown = [
            'active'    => (clone $base())->where('status', User::STATUS_ACTIVE)->count(),
            'suspended' => (clone $base())->where('status', User::STATUS_SUSPENDED)->count(),
        ];

        $query = $base()->with('roles')->orderByDesc('created_at');

        if ($paginate) {
            $page    = max(1, (int) ($filters['page'] ?? 1));
            $perPage = in_array((int) ($filters['per_page'] ?? 10), [10, 25, 50]) ? (int) $filters['per_page'] : 10;
            $totalRows  = (clone $query)->count();
            $totalPages = max(1, (int) ceil($totalRows / $perPage));
            $users      = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        } else {
            $users      = $query->get();
            $totalRows  = $users->count();
            $totalPages = 1;
            $page       = 1;
            $perPage    = $totalRows;
        }

        return [
            'kpis' => [
                'totalNew'       => $totalNew,
                'activeCount'    => $activeCount,
                'suspendedCount' => $suspendedCount,
                'verifiedCount'  => $verifiedCount,
                'newEnrollments' => $newEnrollments,
            ],
            'roleBreakdown'   => $roleBreakdown,
            'statusBreakdown' => $statusBreakdown,
            'users'           => $users,
            'total'           => $totalRows,
            'totalPages'      => $totalPages,
            'curPage'         => $page,
            'perPage'         => $perPage,
        ];
    }
}
