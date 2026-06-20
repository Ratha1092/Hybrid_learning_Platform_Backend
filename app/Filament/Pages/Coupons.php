<?php

namespace App\Filament\Pages;

use App\Domains\Promotions\Models\Coupon;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Number;

class Coupons extends Page
{
    protected string $view = 'filament.pages.coupons';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;
    protected static ?string $navigationLabel = 'Coupons';
    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'coupons';

    public static function canAccess(): bool
    {
        return PanelAccess::can('coupons.view');
    }

    public function mount(): void
    {
        NavBadge::markSeen('coupons');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('coupons', fn (?\Carbon\Carbon $since) => $since
            ? Coupon::where('created_at', '>', $since)->count()
            : Coupon::count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New coupons since you last viewed this page';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $search  = request('search', '');
        $status  = request('status', 'all');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $base = fn () => Coupon::query();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count()],
            ['key' => 'active',   'label' => 'Active',   'count' => $base()->active()->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count()],
            ['key' => 'expired',  'label' => 'Expired',  'count' => $base()->whereNotNull('expires_at')->where('expires_at', '<=', now())->count()],
            ['key' => 'disabled', 'label' => 'Disabled', 'count' => $base()->where('is_active', false)->count()],
        ];

        $query = Coupon::query();

        if ($status === 'active') {
            $query->active()->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
        } elseif ($status === 'expired') {
            $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
        } elseif ($status === 'disabled') {
            $query->where('is_active', false);
        }

        if ($search) {
            $query->where('code', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $coupons    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'status', 'search', 'coupons', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
