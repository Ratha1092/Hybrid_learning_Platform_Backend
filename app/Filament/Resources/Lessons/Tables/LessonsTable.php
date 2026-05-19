<?php

namespace App\Filament\Resources\Lessons\Tables;

use App\Domains\Courses\Models\Lesson;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('section.title')
                    ->label('Section')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Lesson::TYPE_VIDEO => 'success',
                        Lesson::TYPE_ARTICLE => 'info',
                        Lesson::TYPE_QUIZ => 'warning',
                        Lesson::TYPE_LIVE => 'danger',
                        Lesson::TYPE_ASSIGNMENT => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('duration')
                    ->suffix(' min'),
                Tables\Columns\IconColumn::make('is_preview')
                    ->boolean()
                    ->label('Preview'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y'),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Lesson::TYPE_VIDEO => 'Video',
                        Lesson::TYPE_ARTICLE => 'Article',
                        Lesson::TYPE_QUIZ => 'Quiz',
                        Lesson::TYPE_LIVE => 'Live',
                        Lesson::TYPE_ASSIGNMENT => 'Assignment',
                    ]),
            ])

            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ]);
    }
}