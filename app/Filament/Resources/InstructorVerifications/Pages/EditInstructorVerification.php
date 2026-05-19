<?php

namespace App\Filament\Resources\InstructorVerifications\Pages;

use App\Filament\Resources\InstructorVerificationResource;
use Filament\Resources\Pages\EditRecord;

class EditInstructorVerification extends EditRecord
{
    protected static string $resource = InstructorVerificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}