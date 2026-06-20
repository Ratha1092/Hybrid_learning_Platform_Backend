<?php

namespace App\Filament\Resources\Instructors;

use App\Domains\Users\Models\User;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstructorResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Instructors';
    protected static ?string $modelLabel = 'Instructor';
    protected static ?string $pluralModelLabel = 'Instructors';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'instructors';
    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('instructorVerification.status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'approved'  => 'Verified',
                        'pending'   => 'Pending',
                        'rejected'  => 'Rejected',
                        'suspended' => 'Suspended',
                        default     => ucfirst($state ?? 'Unknown'),
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'approved'  => 'success',
                        'pending'   => 'warning',
                        'rejected', 'suspended' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('courses_count')
                    ->counts('courses')
                    ->label('Courses')
                    ->icon('heroicon-m-academic-cap')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('instructor_status')
                    ->label('Status')
                    ->options([
                        'approved' => 'Verified',
                        'pending'  => 'Pending',
                        'rejected' => 'Rejected',
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'],
                        fn (Builder $q, $value) => $q->whereHas('instructorVerification', fn ($q2) => $q2->where('status', $value))
                    )),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('instructor');
    }

    public static function getPages(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('users.view');
    }
}
