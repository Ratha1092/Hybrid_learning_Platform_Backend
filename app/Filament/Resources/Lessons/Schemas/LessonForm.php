<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Domains\Courses\Models\Section;
use Filament\Forms;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('section_id')
                    ->label('Section')
                    ->options(
                        Section::query()
                            ->pluck('title', 'id')
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description'),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('video')
                    ->directory('lessons/videos'),
                Forms\Components\FileUpload::make('attachment')
                    ->directory('lessons/attachments'),
                Forms\Components\TextInput::make('duration')
                    ->numeric()
                    ->suffix('minutes'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('is_preview')
                    ->label('Free Preview'),
            ]);
    }
}