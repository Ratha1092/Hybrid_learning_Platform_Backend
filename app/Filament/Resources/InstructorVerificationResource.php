<?php

namespace App\Filament\Resources;

use App\Domains\Users\Models\InstructorVerification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Notification;
use App\Filament\Resources\InstructorVerifications\Pages;
use Filament\Schemas\Schema;
use Filament\Actions;
use BackedEnum;
use UnitEnum;
use Filament\Tables\Table;

class InstructorVerificationResource extends Resource
{
    protected static ?string $model = InstructorVerification::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Instructor Verifications';
    protected static string|\UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 3;
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Applicant Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Instructor')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Placeholder::make('email')
                            ->label('Email')
                            ->content(fn ($get) =>
                                \App\Domains\Users\Models\User::find($get('user_id'))?->email
                                    ?? '-'
                            ),
                    ])
                    ->columns(1),
            Section::make('Professional Details')->schema([
                Forms\Components\Textarea::make('bio')
                    ->label('Professional Bio')
                    ->disabled()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('experience')
                    ->label('Teaching Experience')
                    ->disabled()
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('institution')
                    ->label('Institution/Organization')
                    ->disabled(),
                Forms\Components\TextInput::make('qualification_type')
                    ->label('Qualification Type')
                    ->disabled(),
                Forms\Components\TextInput::make('completion_year')
                    ->label('Completion Year')
                    ->disabled(),
                Forms\Components\TextInput::make('portfolio_url')
                    ->label('Portfolio URL')
                    ->disabled()
                    ->columnSpan('full'),
            ]),

            Section::make('Verification Status')->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Rejection Reason (if rejected)')
                    ->visible(fn ($get) => $get('status') === 'rejected'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('qualification_type')
                    ->label('Qualification')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->colors([
                        'info' => 'degree',
                        'success' => 'certification',
                        'warning' => 'professional_experience',
                    ]),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (InstructorVerification $record) => $record->status === 'pending')
                    ->action(function (InstructorVerification $record) {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $record->user->update(['instructor_status' => 'verified']);

                        Notification::make()
                            ->title('Instructor Approved')
                            ->body("{$record->user->name} has been approved as an instructor.")
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (InstructorVerification $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Explain why this application is being rejected...'),
                    ])
                    ->action(function (InstructorVerification $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        $record->user->update(['instructor_status' => 'rejected']);

                        Notification::make()
                            ->title('Application Rejected')
                            ->body('The application has been rejected.')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Applicant')->schema([
                TextEntry::make('user.name'),
                TextEntry::make('user.email'),
            ])->columns(2),

            Section::make('Professional Information')->schema([
                TextEntry::make('bio'),
                TextEntry::make('experience'),
                TextEntry::make('qualification_type'),
                TextEntry::make('institution'),
                TextEntry::make('completion_year'),
                TextEntry::make('portfolio_url')
                    ->url(fn ($record) => $record->portfolio_url)
                    ->openUrlInNewTab(),
            ])->columns(2),

            Section::make('Documents')->schema([
                ImageEntry::make('certificate_file')->label('Certificate'),
                ImageEntry::make('identity_file')->label('ID Proof'),
            ])->columns(2),

            Section::make('Review Status')->schema([
                TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                TextEntry::make('rejection_reason'),
                TextEntry::make('reviewer.name')->label('Reviewed By'),
                TextEntry::make('reviewed_at')->dateTime(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstructorVerifications::route('/'),
            'view' => Pages\ViewInstructorVerification::route('/{record}'),
            'edit' => Pages\EditInstructorVerification::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Instructor Verification';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Instructor Verifications';
    }
}
