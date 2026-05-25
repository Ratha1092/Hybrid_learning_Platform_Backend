<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domains\Courses\Models\Course;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
