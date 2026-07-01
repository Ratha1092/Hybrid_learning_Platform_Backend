<?php

namespace App\Filament\Pages;

use App\Domains\Orders\Models\Order;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Orders extends Page
{
    protected string $view = 'filament.pages.orders';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Orders';
    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'orders';

    public static function canAccess(): bool
    {
        return PanelAccess::can('orders.view');
    }

    public function mount(): void
    {
        NavBadge::markSeen('orders');

        auth()->user()?->unreadNotifications()
            ->whereJsonContains('data->type', 'order')
            ->update(['read_at' => now()]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('orders', fn (?\Carbon\Carbon $since) => $since
            ? Order::where('created_at', '>', $since)->count()
            : Order::count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New orders since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public string $tab = 'all';
    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
    }

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
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

        $base = fn() => Order::withoutTrashed();

        $tabs = [
            ['key' => 'all',       'label' => 'All',       'count' => $base()->count(),                            'color' => '#059669'],
            ['key' => 'pending',   'label' => 'Pending',   'count' => $base()->where('status', 'pending')->count(),   'color' => '#fbbf24'],
            ['key' => 'completed', 'label' => 'Completed', 'count' => $base()->where('status', 'completed')->count(), 'color' => '#34d399'],
            ['key' => 'cancelled', 'label' => 'Cancelled', 'count' => $base()->where('status', 'cancelled')->count(), 'color' => '#f87171'],
            ['key' => 'refunded',  'label' => 'Refunded',  'count' => $base()->where('status', 'refunded')->count(),  'color' => '#a78bfa'],
        ];

        $query = Order::withoutTrashed()
            ->with('user:id,name')
            ->withCount('items');

        if ($tab !== 'all' && in_array($tab, ['pending', 'completed', 'cancelled', 'refunded'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $orders     = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'orders', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
