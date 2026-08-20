<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Notifications\DatabaseNotification;

class Notifications extends Page
{
    protected string $view = 'filament.pages.notifications';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Notifications';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'notifications';

    // The nav badge stays personal — "things addressed to me" — even though
    // the page itself is a platform-wide audit log of everyone's notifications.
    public static function getNavigationBadge(): ?string
    {
        $unread = (int) (auth()->user()?->unreadNotifications()->count() ?? 0);

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    // Mirrors the mark-as-read the bell dropdown does, and the same pattern
    // Orders/Payouts/etc. use for their own nav badges — visiting this page
    // is itself an acknowledgment. Only ever touches the viewing admin's own
    // notifications, never anyone else's shown further down the audit table.
    public function mount(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public string $tab = 'all';
    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
        $this->page = 1;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        // Platform-wide audit log — every notification sent to every user,
        // not just the currently logged-in admin's own.
        $base = fn () => DatabaseNotification::query();

        $tabs = [
            ['key' => 'all',    'label' => 'All',    'count' => $base()->count(),                          'color' => '#9333ea'],
            ['key' => 'unread', 'label' => 'Unread', 'count' => $base()->whereNull('read_at')->count(),     'color' => '#f87171'],
            ['key' => 'read',   'label' => 'Read',   'count' => $base()->whereNotNull('read_at')->count(),  'color' => '#34d399'],
        ];

        $query = DatabaseNotification::query()->with('notifiable');

        if ($tab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($tab === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('data', 'ilike', "%{$search}%")
                    ->orWhereHasMorph('notifiable', ['App\\Domains\\Users\\Models\\User'], function ($q2) use ($search) {
                        $q2->where('name', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%");
                    });
            });
        }

        $query->latest();

        $total         = $query->count();
        $totalPages    = max(1, (int) ceil($total / $perPage));
        $curPage       = min($page, $totalPages);
        $notifications = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'notifications', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
