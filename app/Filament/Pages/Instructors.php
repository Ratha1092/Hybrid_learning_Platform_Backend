<?php

namespace App\Filament\Pages;

use App\Domains\Users\Models\User;
use App\Support\NavBadge;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class Instructors extends Page
{
    protected string $view = 'filament.pages.instructors';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Instructors';
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'instructors';

    public static function canAccess(): bool
    {
        return PanelAccess::can('users.view');
    }

    public function mount(): void
    {
        NavBadge::markSeen('instructors');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = NavBadge::countSince('instructors', fn (?\Carbon\Carbon $since) => $since
            ? User::role('instructor')->where('created_at', '>', $since)->count()
            : User::role('instructor')->count());

        return $count > 0 ? Number::abbreviate($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New instructors since you last viewed this page';
    }

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

        $verifiedCount = fn () => User::role('instructor')->whereHas('instructorVerification', fn ($q) => $q->where('status', 'approved'))->count();
        $pendingCount  = fn () => User::role('instructor')->whereHas('instructorVerification', fn ($q) => $q->where('status', 'pending'))->count();
        $rejectedCount = fn () => User::role('instructor')->whereHas('instructorVerification', fn ($q) => $q->where('status', 'rejected'))->count();

        $tabs = [
            ['key' => 'all',      'label' => 'All',      'count' => User::role('instructor')->count(), 'color' => '#2563eb'],
            ['key' => 'verified', 'label' => 'Verified',  'count' => $verifiedCount(), 'color' => '#34d399'],
            ['key' => 'pending',  'label' => 'Pending',   'count' => $pendingCount(),  'color' => '#fbbf24'],
            ['key' => 'rejected', 'label' => 'Rejected',  'count' => $rejectedCount(), 'color' => '#f87171'],
        ];

        $query = User::role('instructor')->withCount('courses');

        if ($tab !== 'all' && in_array($tab, ['verified', 'pending', 'rejected'])) {
            $statusMap = ['verified' => 'approved', 'pending' => 'pending', 'rejected' => 'rejected'];
            $query->whereHas('instructorVerification', fn ($q) => $q->where('status', $statusMap[$tab]));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        $total       = $query->count();
        $totalPages  = max(1, (int) ceil($total / $perPage));
        $curPage     = min($page, $totalPages);
        $instructors = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'instructors', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
