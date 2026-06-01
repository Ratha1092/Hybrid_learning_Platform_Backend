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
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'notifications';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $tab     = request('tab', 'all');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $base = fn() => DatabaseNotification::query();

        $tabs = [
            ['key' => 'all',    'label' => 'All',    'count' => $base()->count(),                           'color' => '#9333ea'],
            ['key' => 'unread', 'label' => 'Unread', 'count' => $base()->whereNull('read_at')->count(),     'color' => '#f87171'],
            ['key' => 'read',   'label' => 'Read',   'count' => $base()->whereNotNull('read_at')->count(),  'color' => '#34d399'],
        ];

        $query = DatabaseNotification::query()->with('notifiable');

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
