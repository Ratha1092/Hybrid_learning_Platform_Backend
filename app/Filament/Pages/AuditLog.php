<?php

namespace App\Filament\Pages;

use App\Domains\Auth\Models\ActivityLog;
use Filament\Pages\Page;

class AuditLog extends Page
{
    protected string $view = 'filament.pages.audit-log';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel                 = 'Audit Log';
    protected static string|\UnitEnum|null $navigationGroup   = 'System';
    protected static ?int    $navigationSort                  = 2;
    protected static ?string $slug                            = 'audit-log';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $action  = request('action', 'all');
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 25);
        $from    = request('from', '');
        $to      = request('to', '');

        if (!in_array($perPage, [25, 50, 100])) {
            $perPage = 25;
        }

        $actions = [
            'all'              => ['label' => 'All',              'color' => '#6366f1'],
            'login'            => ['label' => 'Login',            'color' => '#10b981'],
            'registration'     => ['label' => 'Registration',     'color' => '#3b82f6'],
            'failed_login'     => ['label' => 'Failed Login',     'color' => '#f87171'],
            'password_changed' => ['label' => 'Password Changed', 'color' => '#f59e0b'],
            'email_verified'   => ['label' => 'Email Verified',   'color' => '#06b6d4'],
            '2fa_enabled'      => ['label' => '2FA Enabled',      'color' => '#8b5cf6'],
            '2fa_disabled'     => ['label' => '2FA Disabled',     'color' => '#ec4899'],
        ];

        $query = ActivityLog::with('user')->latest();

        if ($action !== 'all' && array_key_exists($action, $actions)) {
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
