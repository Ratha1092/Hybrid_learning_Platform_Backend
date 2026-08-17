<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Domains\Courses\Models\Section;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;
    protected string $view = 'filament.resources.lessons.edit-lesson';

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
        return route('filament.admin.pages.lessons');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['type'] ?? null) === 'article') {
            $data['duration'] = null;
        }

        return $data;
    }

    protected function getViewData(): array
    {
        $record = $this->record;

        return [
            'backUrl'       => route('filament.admin.pages.lessons'),
            'sectionTitle'  => $record->section?->title ?? '—',
            'courseTitle'   => $record->section?->course?->title ?? '—',
            'progressCount' => $record->progress()->count(),
            'sections'      => Section::with('course')->orderBy('title')->get()
                                ->mapWithKeys(fn ($s) => [$s->id => $s->course?->title . ' › ' . $s->title]),
        ];
    }
}
