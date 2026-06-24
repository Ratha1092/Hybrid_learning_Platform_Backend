<?php

namespace App\Filament\Pages;

use App\Domains\Auth\Models\ActivityLog;
use App\Support\CsvExporter;
use App\Support\PanelAccess;
use Filament\Pages\Page;

class AuditLog extends Page
{
    protected string $view = 'filament.pages.audit-log';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel                 = 'Audit Logs';
    protected static string|\UnitEnum|null $navigationGroup   = 'Security';
    protected static ?int    $navigationSort                  = 1;
    protected static ?string $slug                            = 'audit-log';

    public const ACTIONS = [
        // Auth & access
        'all'              => ['label' => 'All',              'color' => '#6366f1'],
        'login'            => ['label' => 'Login',            'color' => '#10b981'],
        'registration'     => ['label' => 'Registration',     'color' => '#3b82f6'],
        'failed_login'     => ['label' => 'Failed Login',     'color' => '#f87171'],
        'account_locked'   => ['label' => 'Account Locked',   'color' => '#ef4444'],
        'password_changed' => ['label' => 'Password Changed', 'color' => '#f59e0b'],
        'email_verified'   => ['label' => 'Email Verified',   'color' => '#06b6d4'],
        '2fa_enabled'      => ['label' => '2FA Enabled',      'color' => '#8b5cf6'],
        '2fa_disabled'     => ['label' => '2FA Disabled',     'color' => '#ec4899'],
        // Users & roles
        'role.created'     => ['label' => 'Role Created',     'color' => '#7c3aed'],
        'role.updated'     => ['label' => 'Role Updated',     'color' => '#7c3aed'],
        'user.roles_changed' => ['label' => 'User Roles Changed', 'color' => '#0ea5e9'],
        'user.banned'      => ['label' => 'User Banned',      'color' => '#ef4444'],
        'user.unbanned'    => ['label' => 'User Unbanned',    'color' => '#3b82f6'],
        // Courses
        'course.created'   => ['label' => 'Course Created',   'color' => '#22d3ee'],
        'course.published' => ['label' => 'Course Published', 'color' => '#10b981'],
        'course.rejected'  => ['label' => 'Course Rejected',  'color' => '#f87171'],
        'instructor_verification.approved' => ['label' => 'Instructor Approved', 'color' => '#34d399'],
        'instructor_verification.rejected' => ['label' => 'Instructor Rejected', 'color' => '#f87171'],
        // Orders & payments
        'order.placed'     => ['label' => 'Order Placed',     'color' => '#34d399'],
        'order.cancelled'  => ['label' => 'Order Cancelled',  'color' => '#fb923c'],
        'payment.completed'=> ['label' => 'Payment Completed','color' => '#10b981'],
        // Payouts
        'payout.approved'  => ['label' => 'Payout Approved',  'color' => '#34d399'],
        'payout.rejected'  => ['label' => 'Payout Rejected',  'color' => '#f87171'],
        // Settings & other
        'settings.updated' => ['label' => 'Settings Updated', 'color' => '#fb923c'],
        'settings.finance_updated' => ['label' => 'Finance Settings Updated', 'color' => '#fb923c'],
        'coupon.created'   => ['label' => 'Coupon Created',   'color' => '#6366f1'],
        'coupon.updated'   => ['label' => 'Coupon Updated',   'color' => '#6366f1'],
    ];

    public static function canAccess(): bool
    {
        return PanelAccess::can('reports.view_audit');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function exportCsv(): void
    {
        $query = static::buildQuery($this->currentFilters());

        $header = ['User', 'Email', 'Action', 'Subject Type', 'Subject ID', 'IP Address', 'User Agent', 'Created At'];

        $rows = $query->get()->map(fn (ActivityLog $log) => [
            $log->user?->name ?? '',
            $log->user?->email ?? '',
            static::ACTIONS[$log->action]['label'] ?? $log->action,
            $log->subject_type ?? '',
            $log->subject_id ?? '',
            $log->ip_address ?? '',
            $log->user_agent ?? '',
            $log->created_at?->format('M d, Y H:i:s') ?? '',
        ])->all();

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: 'audit-log-' . now()->format('Y-m-d') . '.csv',
        );
    }

    private function currentFilters(): array
    {
        return [
            'action' => request('action', 'all'),
            'search' => request('search', ''),
            'from' => request('from', ''),
            'to' => request('to', ''),
        ];
    }

    private static function buildQuery(array $filters)
    {
        $action = $filters['action'] ?? 'all';
        $search = $filters['search'] ?? '';
        $from = $filters['from'] ?? '';
        $to = $filters['to'] ?? '';

        $query = ActivityLog::with('user')->latest();

        if ($action !== 'all' && array_key_exists($action, self::ACTIONS)) {
            $query->where('action', $action);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) =>
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                  );
            });
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    protected function getViewData(): array
    {
        $filters = $this->currentFilters();
        $action  = $filters['action'];
        $search  = $filters['search'];
        $from    = $filters['from'];
        $to      = $filters['to'];
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 25);

        if (!in_array($perPage, [25, 50, 100])) {
            $perPage = 25;
        }

        $actions = self::ACTIONS;
        $query = static::buildQuery($filters);

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $logs       = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $tabs = collect($actions)->map(function ($meta, $key) use ($action) {
            return [
                'key'    => $key,
                'label'  => $meta['label'],
                'color'  => $meta['color'],
                'active' => $action === $key,
            ];
        })->values()->all();

        return compact('logs', 'tabs', 'action', 'actions', 'search', 'from', 'to',
                       'total', 'totalPages', 'curPage', 'perPage');
    }
}
