<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLesson extends ViewRecord
{
    protected static string $resource = LessonResource::class;
    protected string $view = 'filament.resources.lessons.view-lesson';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        $record = $this->record;

        return [
            'backUrl'       => route('filament.admin.pages.lessons'),
            'sectionTitle'  => $record->section?->title ?? '—',
            'courseTitle'   => $record->section?->course?->title ?? '—',
            'progressCount' => $record->progress()->count(),
        ];
    }
}
