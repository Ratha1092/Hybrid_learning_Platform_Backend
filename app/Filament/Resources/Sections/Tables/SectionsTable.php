<?php

namespace App\Filament\Resources\Sections\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lessons_count')
                    ->counts('lessons')
                    ->label('Lessons'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y'),
            ])

            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ]);
    }
}