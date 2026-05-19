<?php

namespace App\Filament\Resources\Sections\Schemas;

use App\Domains\Courses\Models\Course;
use Filament\Forms;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('course_id')
                    ->label('Course')
                    ->options(
                        Course::query()
                            ->pluck('title', 'id')
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(1),
            ]);
    }
}