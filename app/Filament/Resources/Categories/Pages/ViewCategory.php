<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;
    protected string $view = 'filament.resources.categories.view-category';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
