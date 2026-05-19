<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Domains\Courses\Models\Course;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('thumbnail')
                                    ->label('')
                                    ->height(220),
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('title')
                                            ->size('xl')
                                            ->weight('bold'),
                                        TextEntry::make('short_description')
                                            ->placeholder('No short description'),
                                        TextEntry::make('status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                Course::STATUS_DRAFT => 'gray',
                                                Course::STATUS_PENDING => 'warning',
                                                Course::STATUS_PUBLISHED => 'success',
                                                Course::STATUS_REJECTED => 'danger',
                                                Course::STATUS_ARCHIVED => 'gray',

                                                default => 'gray',
                                            }),
                                        TextEntry::make('is_published')
                                            ->badge()
                                            ->formatStateUsing(
                                                fn (bool $state): string =>
                                                $state ? 'Published' : 'Unpublished'
                                            )
                                            ->color(
                                                fn (bool $state): string =>
                                                $state ? 'success' : 'gray'
                                            ),
                                        TextEntry::make('price')
                                            ->money('USD'),
                                        TextEntry::make('level')
                                            ->badge(),
                                        TextEntry::make('language')
                                            ->badge(),
                                            TextEntry::make('visibility')
                                            ->badge(),

                                        TextEntry::make('preview_video_url')
                                            ->url(
                                                fn ($state) => $state
                                            )
                                            ->openUrlInNewTab(),
                                        TextEntry::make('certificate_enabled')
                                            ->badge()
                                            ->formatStateUsing(
                                                fn (bool $state): string =>
                                                $state ? 'Enabled' : 'Disabled'
                                            ),
                                    ]),
                            ]),
                    ]),
                Section::make('Course Information')
                    ->schema([
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('requirements')
                            ->placeholder('No requirements'),
                        TextEntry::make('what_you_will_learn')
                            ->placeholder('No learning outcomes'),
                    ])

                    ->columns(2),
                Section::make('Instructor')
                    ->schema([
                        TextEntry::make('instructor.name')
                            ->label('Instructor'),
                        TextEntry::make('instructor.email')
                            ->label('Email'),
                        TextEntry::make('approvedBy.name')
                            ->label('Approved By')
                            ->placeholder('Not approved'),
                        TextEntry::make('approved_at')
                            ->dateTime()
                            ->placeholder('Not approved'),
                    ])
                    ->columns(2),
                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('sections_count')
                            ->label('Sections')
                            ->state(
                                fn ($record) =>
                                $record->sections()->count()
                            ),
                        TextEntry::make('lessons_count')
                            ->label('Lessons')
                            ->state(
                                fn ($record) =>
                                $record->lessons()->count()
                            ),
                        TextEntry::make('enrollments_count')
                            ->label('Enrollments')
                            ->state(
                                fn ($record) =>
                                $record->enrollments()->count()
                            ),
                        TextEntry::make('reviews_count')
                            ->label('Reviews')
                            ->state(
                                fn ($record) =>
                                $record->reviews()->count()
                            ),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }
}