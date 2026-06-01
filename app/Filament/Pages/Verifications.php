<?php

namespace App\Filament\Pages;

use App\Domains\Users\Models\InstructorVerification;
use BackedEnum;
use Filament\Pages\Page;

class Verifications extends Page
{
    protected string $view = 'filament.pages.verifications';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Verifications';
    protected static string|\UnitEnum|null $navigationGroup = 'Users';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'instructor-verifications';

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

        $base = fn() => InstructorVerification::withoutTrashed();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => $base()->count(),                                 'color' => '#ea580c'],
            ['key' => 'pending',  'label' => 'Pending',  'count' => $base()->where('status', 'pending')->count(),     'color' => '#fbbf24'],
            ['key' => 'approved', 'label' => 'Approved', 'count' => $base()->where('status', 'approved')->count(),    'color' => '#34d399'],
            ['key' => 'rejected', 'label' => 'Rejected', 'count' => $base()->where('status', 'rejected')->count(),    'color' => '#f87171'],
        ];

        $query = InstructorVerification::withoutTrashed()
            ->with('user:id,name,email');

        if ($tab !== 'all' && in_array($tab, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($q2) => $q2->where('email', 'like', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');

        $total         = $query->count();
        $totalPages    = max(1, (int) ceil($total / $perPage));
        $curPage       = min($page, $totalPages);
        $verifications = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'verifications', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
