<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Filament\Resources\Sections\SectionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSection extends ViewRecord
{
    protected static string $resource = SectionResource::class;
    protected string $view = 'filament.resources.sections.view-section';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
