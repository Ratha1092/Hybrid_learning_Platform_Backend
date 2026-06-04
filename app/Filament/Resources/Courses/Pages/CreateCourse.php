<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Courses')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(url('/admin/courses')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return url('/admin/courses/' . $this->record->id);
    }
}
