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

    public string $search = '';
    public int $page = 1;
    public int $perPage = 10;

    public function updatedSearch(): void
    {
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
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $query = InstructorWallet::with('instructor:id,name,email');

        if ($search) {
            $query->whereHas('instructor', fn ($q) => $q
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%"));
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
