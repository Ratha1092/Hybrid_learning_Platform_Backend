<?php

namespace App\Filament\Pages;

use App\Domains\Learning\Models\ContentReport;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ContentReports extends Page
{
    protected string $view = 'filament.pages.content-reports';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Content Reports';
    protected static string|\UnitEnum|null $navigationGroup = 'People';
    protected static ?int $navigationSort = 9;
    protected static ?string $slug = 'content-reports';

    public static function canAccess(): bool
    {
        return PanelAccess::can('content_reports.view');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = ContentReport::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
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

    public function selectTab(string $tab): void
    {
        $this->tab = $tab;
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

    public function markReviewed(int $id): void
    {
        if (! PanelAccess::can('content_reports.update')) {
            return;
        }

        $report = ContentReport::findOrFail($id);
        if ($report->status !== 'pending') return;

        $report->update([
            'status'      => 'reviewed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Notification::make()->title('Report marked as reviewed.')->success()->send();
    }

    public function dismiss(int $id): void
    {
        if (! PanelAccess::can('content_reports.update')) {
            return;
        }

        $report = ContentReport::findOrFail($id);
        if ($report->status !== 'pending') return;

        $report->update([
            'status'      => 'dismissed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Notification::make()->title('Report dismissed.')->send();
    }

    protected function getViewData(): array
    {
        $tab     = $this->tab;
        $search  = $this->search;
        $page    = max(1, $this->page);
        $perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;

        $base = fn () => ContentReport::query();

        $tabs = [
            ['key' => 'all',       'label' => 'All',       'count' => $base()->count(),                             'color' => '#2563eb'],
            ['key' => 'pending',   'label' => 'Pending',   'count' => $base()->where('status', 'pending')->count(),   'color' => '#fbbf24'],
            ['key' => 'reviewed',  'label' => 'Reviewed',  'count' => $base()->where('status', 'reviewed')->count(),  'color' => '#34d399'],
            ['key' => 'dismissed', 'label' => 'Dismissed', 'count' => $base()->where('status', 'dismissed')->count(), 'color' => '#f87171'],
        ];

        $query = ContentReport::query()->with(['reporter:id,name,email', 'reviewer:id,name', 'reportable']);

        if ($tab !== 'all' && in_array($tab, ['pending', 'reviewed', 'dismissed'])) {
            $query->where('status', $tab);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'ilike', "%{$search}%")
                  ->orWhereHas('reporter', fn ($q2) => $q2->where('name', 'ilike', "%{$search}%"))
                  ->orWhereHas('reporter', fn ($q2) => $q2->where('email', 'ilike', "%{$search}%"));
            });
        }

        $query->orderBy('id', 'desc');
        $total      = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $curPage    = min($page, $totalPages);
        $reports    = $query->skip(($curPage - 1) * $perPage)->take($perPage)->get();

        return compact('tabs', 'tab', 'search', 'reports', 'total', 'totalPages', 'curPage', 'perPage');
    }
}
