<?php

namespace App\Filament\Pages;

use App\Domains\Finance\Models\InstructorWallet;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;

class Wallets extends Page
{
    protected string $view = 'filament.pages.wallets';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Wallets';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'wallets';

    public static function canAccess(): bool
    {
        return PanelAccess::can('wallets.view');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected function getViewData(): array
    {
        $search  = request('search', '');
        $page    = max(1, (int) request('page', 1));
        $perPage = (int) request('per_page', 10);

        if (!in_array($perPage, [10, 25, 50])) $perPage = 10;

        $query = InstructorWallet::with('instructor:id,name,email');

        if ($search) {
            $query->whereHas('instructor', fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $query->orderByDesc('balance');

        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $wallets    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        $totals = [
            'balance'         => (float) InstructorWallet::sum('balance'),
            'pending_balance' => (float) InstructorWallet::sum('pending_balance'),
            'count'           => InstructorWallet::count(),
        ];

        return compact('search', 'wallets', 'total', 'totalPages', 'curPage', 'perPage', 'totals');
    }
}
