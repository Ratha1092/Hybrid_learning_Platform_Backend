<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Infolists\Components\TextEntry;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Section::make('Lesson Overview')

                    ->schema([

                        TextEntry::make('title')
                            ->size('xl')
                            ->weight('bold'),
                        TextEntry::make('type')
                            ->badge(),
                        TextEntry::make('duration')
                            ->suffix(' minutes'),
                        TextEntry::make('is_preview')
                            ->badge()
                            ->formatStateUsing(
                                fn (bool $state): string =>
                                $state ? 'Free Preview' : 'Premium'
                            ),
                        TextEntry::make('order'),
                    ])
                    ->columns(2),
                Section::make('Content')
                    ->schema([
                        TextEntry::make('content')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Section::make('Video')
                    ->schema([
                        TextEntry::make('video_provider')
                            ->placeholder('No provider'),
                        TextEntry::make('video_url')
                            ->url(
                                fn ($state) => $state
                            )
                            ->openUrlInNewTab(),
                        TextEntry::make('video_path')
                            ->placeholder('No uploaded video'),
                    ])

                    ->columns(2),
                Section::make('Attachment')
                    ->schema([
                        TextEntry::make('attachment_name')
                            ->placeholder('No attachment'),
                        TextEntry::make('attachment')
                            ->placeholder('No file uploaded'),
                    ])

                    ->columns(2),
            ]);
    }
}