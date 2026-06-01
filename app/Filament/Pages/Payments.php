<?php

namespace App\Filament\Pages;

use App\Domains\Payments\Models\Payment;
use BackedEnum;
use Filament\Pages\Page;

class Payments extends Page
{
    protected string $view = 'filament.pages.payments';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Payments';
    protected static string|\UnitEnum|null $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'payments';

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

        $base = fn() => Payment::query();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                                                                                  'color' => '#7c3aed'],
            ['key' => 'pending',  'label' => 'Pending',  'count' => $base()->whereIn('status', ['pending', 'processing'])->count(),                                    'color' => '#fbbf24'],
            ['key' => 'paid',     'label' => 'Paid',     'count' => $base()->whereIn('status', ['paid', 'completed'])->count(),                                         'color' => '#34d399'],
            ['key' => 'failed',   'label' => 'Failed',   'count' => $base()->whereIn('status', ['failed', 'expired'])->count(),                                         'color' => '#f87171'],
            ['key' => 'refunded', 'label' => 'Refunded', 'count' => $base()->where('status', 'refunded')->count(),                                                      'color' => '#a78bfa'],
        ];

        $query = Payment::with(['order:id,order_number,user_id', 'order.user:id,name']);

        if ($tab === 'pending') {
            $query->whereIn('status', ['pending', 'processing']);
        } elseif ($tab === 'paid') {
            $query->whereIn('status', ['paid', 'completed']);
        } elseif ($tab === 'failed') {
            $query->whereIn('status', ['failed', 'expired']);
        } elseif ($tab === 'refunded') {
            $query->where('status', 'refunded');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', fn($q2) => $q2->where('order_number', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $payments   = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'payments', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
