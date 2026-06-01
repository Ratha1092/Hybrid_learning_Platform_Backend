<?php

namespace App\Filament\Resources\Reviews;

use App\Domains\Learning\Models\Review;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;
    protected static ?string $navigationLabel = 'Reviews';
    protected static ?string $modelLabel = 'Review';
    protected static ?string $pluralModelLabel = 'Reviews';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 6;
    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color(fn (int $state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(60)
                    ->wrap()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([5 => '★★★★★', 4 => '★★★★☆', 3 => '★★★☆☆', 2 => '★★☆☆☆', 1 => '★☆☆☆☆']),
            ]);
    }

    public static function getPages(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
