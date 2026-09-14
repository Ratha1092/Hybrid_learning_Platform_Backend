<?php

namespace App\Filament\Pages;

use App\Domains\Orders\Models\Order;
use App\Support\Concerns\HasDateRangePresets;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Orders extends Page
{
    use HasDateRangePresets;

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

    public string $preset = 'all_time';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->preset   = request('preset', 'all_time');
        $this->dateFrom = request('date_from', '');
        $this->dateTo   = request('date_to', '');

        NavBadge::markSeen('orders');
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

        [$from, $to] = static::resolvePreset($this->preset, 'all_time', $this->dateFrom ?: null, $this->dateTo ?: null);

        $base = fn() => static::applyDateRange(Order::withoutTrashed(), 'created_at', $from, $to);

        // One grouped query instead of five separate COUNT round-trips for the tab badges.
        $statusCounts = $base()
            ->toBase()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $tabs = [
            ['key' => 'all',       'label' => 'All',       'count' => $statusCounts->sum(),               'color' => '#059669'],
            ['key' => 'pending',   'label' => 'Pending',   'count' => $statusCounts['pending'] ?? 0,   'color' => '#fbbf24'],
            ['key' => 'completed', 'label' => 'Completed', 'count' => $statusCounts['completed'] ?? 0, 'color' => '#34d399'],
            ['key' => 'cancelled', 'label' => 'Cancelled', 'count' => $statusCounts['cancelled'] ?? 0, 'color' => '#f87171'],
        ];

        $query = $base()
            ->with('user:id,name')
            ->withCount('items');

        if ($tab !== 'all' && in_array($tab, ['pending', 'completed', 'cancelled'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'ilike', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $orders     = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $activePreset      = $this->preset;
        $activePeriodLabel = $this->preset !== 'all_time'
            ? (static::dateRangePresetOptions()[$this->preset] ?? ucfirst($this->preset))
            : null;

        return compact('tabs', 'tab', 'search', 'orders', 'total', 'totalPages', 'curPage', 'perPage', 'activePreset', 'activePeriodLabel');
    }
}
