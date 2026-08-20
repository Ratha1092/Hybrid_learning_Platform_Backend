<?php

namespace App\Filament\Pages;

use App\Domains\Auth\Models\ActivityLog;
use App\Support\PanelAccess;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SecurityLog extends Page
{
    protected string  $view              = 'filament.pages.security-log';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel                 = 'Security Events';
    protected static string|\UnitEnum|null $navigationGroup   = 'Security';
    protected static ?int    $navigationSort                  = 2;
    protected static ?string $slug                            = 'security-log';

    public const SECURITY_ACTIONS = [
        'all'              => ['label' => 'All',               'color' => '#6366f1', 'severity' => 'low'],
        'failed_login'     => ['label' => 'Failed Login',      'color' => '#f87171', 'severity' => 'high'],
        'account_locked'   => ['label' => 'Account Locked',    'color' => '#ef4444', 'severity' => 'critical'],
        'login'            => ['label' => 'Login',             'color' => '#10b981', 'severity' => 'low'],
        'password_changed' => ['label' => 'Password Changed',  'color' => '#f59e0b', 'severity' => 'medium'],
        'user.banned'      => ['label' => 'User Banned',       'color' => '#ef4444', 'severity' => 'high'],
        'user.unbanned'    => ['label' => 'User Unbanned',     'color' => '#3b82f6', 'severity' => 'medium'],
    ];

    public static function canAccess(): bool
    {
        return PanelAccess::can('system.view_security');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $action  = request('action', 'all');
        $search  = trim(request('search', ''));
        $ip      = trim(request('ip', ''));
        $from    = request('from', '');
        $to      = request('to', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = 30;

        if (! array_key_exists($action, self::SECURITY_ACTIONS)) {
            $action = 'all';
        }

        $securityActionKeys = array_keys(self::SECURITY_ACTIONS);
        $securityActionKeys = array_filter($securityActionKeys, fn($k) => $k !== 'all');

        $query = ActivityLog::with('user')
            ->whereIn('action', $securityActionKeys)
            ->latest();

        if ($action !== 'all') {
            $query->where('action', $action);
        }
        if ($ip !== '') {
            $query->where('ip_address', 'ilike', "%{$ip}%");
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'ilike', "%{$search}%")
                  ->orWhereHas('user', fn($u) =>
                      $u->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                  );
            });
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $events     = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        // KPIs (always last 24h)
        $kpis = [
            'failedLoginsToday' => ActivityLog::where('action', 'failed_login')
                ->whereDate('created_at', today())->count(),
            'uniqueAttackerIPs' => ActivityLog::where('action', 'failed_login')
                ->where('created_at', '>=', now()->subDay())
                ->distinct('ip_address')->count('ip_address'),
            'lockedAccounts' => ActivityLog::where('action', 'account_locked')
                ->whereDate('created_at', today())->count(),
            'successfulLoginsToday' => ActivityLog::where('action', 'login')
                ->whereDate('created_at', today())->count(),
        ];

        // IP risk table — IPs with 5+ failed logins in last 24h
        $riskyIps = ActivityLog::select('ip_address', DB::raw('count(*) as attempts'), DB::raw('max(created_at) as last_seen'))
            ->where('action', 'failed_login')
            ->where('created_at', '>=', now()->subDay())
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->havingRaw('count(*) >= 3')
            ->orderByRaw('count(*) desc')
            ->limit(10)
            ->get();

        return compact(
            'events', 'kpis', 'riskyIps',
            'total', 'totalPages', 'curPage', 'perPage',
            'action', 'search', 'ip', 'from', 'to'
        );
    }
}
