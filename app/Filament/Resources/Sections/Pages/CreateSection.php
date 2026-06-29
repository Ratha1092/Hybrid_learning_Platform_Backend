<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Domains\Courses\Models\Course;
use App\Filament\Resources\Sections\SectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSection extends CreateRecord
{
    protected static string $resource = SectionResource::class;

    protected string $view = 'filament.resources.sections.create-section';

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'backUrl' => route('filament.admin.pages.sections'),
            'courses' => Course::orderBy('title')->pluck('title', 'id'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.sections');
    }
}
