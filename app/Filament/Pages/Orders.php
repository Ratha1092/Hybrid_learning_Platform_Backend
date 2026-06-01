<?php

namespace App\Filament\Pages;

use App\Domains\Orders\Models\Order;
use BackedEnum;
use Filament\Pages\Page;

class Orders extends Page
{
    protected string $view = 'filament.pages.orders';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Orders';
    protected static string|\UnitEnum|null $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'orders';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $tab     = request('tab', 'all');
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

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
