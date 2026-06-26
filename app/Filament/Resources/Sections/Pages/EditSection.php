<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Domains\Courses\Models\Course;
use App\Filament\Resources\Sections\SectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;
    protected string $view = 'filament.resources.sections.edit-section';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.sections');
    }

    protected function getViewData(): array
    {
        return [
            'backUrl'      => route('filament.admin.pages.sections'),
            'lessonCount'  => $this->record->lessons()->count(),
            'courseTitle'  => $this->record->course?->title ?? '—',
            'courses'      => Course::orderBy('title')->pluck('title', 'id'),
        ];
    }
}
