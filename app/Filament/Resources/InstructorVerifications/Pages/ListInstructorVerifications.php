<?php

namespace App\Filament\Resources\InstructorVerifications\Pages;

use App\Filament\Resources\InstructorVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstructorVerifications extends ListRecords
{
    protected static string $resource = InstructorVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}