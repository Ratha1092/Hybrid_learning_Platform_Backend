<?php

namespace App\Filament\Pages;

use App\Domains\Learning\Models\ContentReport;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ContentReportDetail extends Page
{
    protected string $view = 'filament.pages.content-report-detail';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'content-reports/{record}';

    public ContentReport $report;

    public static function canAccess(): bool
    {
        return PanelAccess::can('content_reports.view');
    }

    public function mount(int|string $record): void
    {
        $this->report = ContentReport::with([
            'reporter:id,name,email',
            'reviewer:id,name',
            'reportable',
        ])->findOrFail($record);
    }

    public function markReviewed(): void
    {
        if (!PanelAccess::can('content_reports.update') || $this->report->status !== 'pending') {
            return;
        }

        $this->report->update([
            'status' => 'reviewed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        $this->report->refresh();

        Notification::make()->title('Report marked as reviewed.')->success()->send();
    }

    public function dismiss(): void
    {
        if (!PanelAccess::can('content_reports.update') || $this->report->status !== 'pending') {
            return;
        }

        $this->report->update([
            'status' => 'dismissed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        $this->report->refresh();

        Notification::make()->title('Report dismissed.')->send();
    }

    protected function getViewData(): array
    {
        $reportableUrl = null;
        if ($this->report->reportable_type === 'course') {
            $reportableUrl = route('filament.admin.resources.courses.view', [
                'record' => $this->report->reportable_id,
            ]);
        } elseif ($this->report->reportable_type === 'review' && $this->report->reportable?->course_id) {
            $reportableUrl = route('filament.admin.pages.reviews', [
                'course_id' => $this->report->reportable->course_id,
            ]);
        }

        return [
            'report' => $this->report,
            'reportableUrl' => $reportableUrl,
            'canUpdate' => PanelAccess::can('content_reports.update'),
        ];
    }
}
