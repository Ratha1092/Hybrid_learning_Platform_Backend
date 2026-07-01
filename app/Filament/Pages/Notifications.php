<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Notifications extends Page
{
    protected string $view = 'filament.pages.notifications';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Notifications';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'notifications';

    public static function getNavigationBadge(): ?string
    {
        $unread = (int) (auth()->user()?->unreadNotifications()->count() ?? 0);

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function mount(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $tab = 'all';
    public int $page = 1;
    public int $perPage = 10;

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
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $user = auth()->user();
        $base = fn() => $user->notifications();

        $tabs = [
            ['key' => 'all',    'label' => 'All',    'count' => $base()->count(),                           'color' => '#9333ea'],
            ['key' => 'unread', 'label' => 'Unread', 'count' => $base()->whereNull('read_at')->count(),     'color' => '#f87171'],
            ['key' => 'read',   'label' => 'Read',   'count' => $base()->whereNotNull('read_at')->count(),  'color' => '#34d399'],
        ];

        $query = $user->notifications();

        if ($tab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($tab === 'read') {
            $query->whereNotNull('read_at');
        }

        $query->latest();

        $total         = $query->count();
        $totalPages    = max(1, (int) ceil($total / $perPage));
        $curPage       = min($page, $totalPages);
        $notifications = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'notifications', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
