<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domains\Courses\Models\Course;
use App\Filament\Resources\Courses\CourseResource;

use App\Filament\Resources\Courses\Widgets\CourseStatsOverview;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        return 'Review, publish, and manage all your courses in one place.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $csv = collect(['ID,Title,Instructor,Category,Price,Status,Students,Created']);
                    Course::withoutGlobalScopes()
                        ->with(['instructor', 'category'])
                        ->withCount('enrollments')
                        ->get()
                        ->each(function ($course) use ($csv) {
                            $csv->push(implode(',', [
                                'COUR-' . str_pad($course->id, 5, '0', STR_PAD_LEFT),
                                '"' . str_replace('"', '""', $course->title) . '"',
                                '"' . ($course->instructor?->name ?? '') . '"',
                                '"' . ($course->category?->name ?? '') . '"',
                                $course->price > 0 ? number_format($course->price, 2) : 'Free',
                                $course->status,
                                $course->enrollments_count,
                                $course->created_at?->format('Y-m-d'),
                            ]));
                        });

                    $filename = 'courses-' . now()->format('Y-m-d') . '.csv';
                    return response()->streamDownload(
                        fn () => print($csv->implode("\n")),
                        $filename,
                        ['Content-Type' => 'text/csv']
                    );
                }),

            CreateAction::make()
                ->label('New Course')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            CourseStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Course::withoutGlobalScopes()->count()),

            'pending' => Tab::make('Pending Review')
                ->badge(Course::withoutGlobalScopes()->where('status', Course::STATUS_PENDING)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('status', Course::STATUS_PENDING)
                ),

            'published' => Tab::make('Published')
                ->badge(Course::withoutGlobalScopes()->where('status', Course::STATUS_PUBLISHED)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('status', Course::STATUS_PUBLISHED)
                ),

            'draft' => Tab::make('Draft')
                ->badge(Course::withoutGlobalScopes()->where('status', Course::STATUS_DRAFT)->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('status', Course::STATUS_DRAFT)
                ),

            'rejected' => Tab::make('Rejected')
                ->badge(Course::withoutGlobalScopes()->where('status', Course::STATUS_REJECTED)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('status', Course::STATUS_REJECTED)
                ),

            'archived' => Tab::make('Archived')
                ->badge(Course::withoutGlobalScopes()->where('status', Course::STATUS_ARCHIVED)->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('status', Course::STATUS_ARCHIVED)
                ),
        ];
    }
}
