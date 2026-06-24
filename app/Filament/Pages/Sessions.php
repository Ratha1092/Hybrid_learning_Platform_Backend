<?php

namespace App\Filament\Pages;

use App\Support\PanelAccess;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Sessions extends Page
{
    protected string  $view              = 'filament.pages.sessions';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel                 = 'Sessions';
    protected static string|\UnitEnum|null $navigationGroup   = 'Security';
    protected static ?int    $navigationSort                  = 3;
    protected static ?string $slug                            = 'sessions';

    public static function canAccess(): bool
    {
        return PanelAccess::can('system.view_security');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function revokeSession(int $id): void
    {
        if (! PanelAccess::can('system.view_security')) {
            return;
        }

        DB::table('user_sessions')->where('id', $id)->delete();

        \Filament\Notifications\Notification::make()
            ->title('Session revoked.')
            ->success()
            ->send();
    }

    public function revokeAllForUser(int $userId): void
    {
        if (! PanelAccess::can('system.view_security')) {
            return;
        }

        DB::table('user_sessions')->where('user_id', $userId)->delete();

        \Filament\Notifications\Notification::make()
            ->title('All sessions revoked for user.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $search  = trim(request('search', ''));
        $page    = max(1, (int) request('page', 1));
        $perPage = 30;

        $query = DB::table('user_sessions')
            ->join('users', 'user_sessions.user_id', '=', 'users.id')
            ->select(
                'user_sessions.id',
                'user_sessions.user_id',
                'user_sessions.ip_address',
                'user_sessions.user_agent',
                'user_sessions.last_activity',
                'user_sessions.created_at',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->orderByDesc('user_sessions.last_activity');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'ilike', "%{$search}%")
                  ->orWhere('users.email', 'ilike', "%{$search}%")
                  ->orWhere('user_sessions.ip_address', 'ilike', "%{$search}%");
            });
        }

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $sessions   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $kpis = [
            'total'       => DB::table('user_sessions')->count(),
            'active_24h'  => DB::table('user_sessions')->where('last_activity', '>=', now()->subDay())->count(),
            'unique_users' => DB::table('user_sessions')->distinct('user_id')->count('user_id'),
            'unique_ips'   => DB::table('user_sessions')->distinct('ip_address')->count('ip_address'),
        ];

        return compact('sessions', 'kpis', 'total', 'totalPages', 'curPage', 'perPage', 'search');
    }
}
