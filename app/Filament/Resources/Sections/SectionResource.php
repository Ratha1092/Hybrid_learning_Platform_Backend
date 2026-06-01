<?php

namespace App\Filament\Resources\Sections;

use App\Domains\Courses\Models\Section;

use App\Filament\Resources\Sections\Pages\CreateSection;
use App\Filament\Resources\Sections\Pages\EditSection;
use App\Filament\Resources\Sections\Pages\ViewSection;
use App\Filament\Resources\Sections\Schemas\SectionForm;
use App\Filament\Resources\Sections\Schemas\SectionInfolist;
use App\Filament\Resources\Sections\Tables\SectionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;
    protected static string|BackedEnum|null $navigationIcon =Heroicon::OutlinedQueueList;
    protected static ?string $navigationLabel = 'Sections';
    protected static ?string $modelLabel = 'Section';
    protected static ?string $pluralModelLabel = 'Sections';
    protected static string|\UnitEnum|null $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute ='title';
    protected static bool $shouldRegisterNavigation = false;
    public static function form(Schema $schema): Schema
    {
        return SectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateSection::route('/create'),
            'view'   => ViewSection::route('/{record}'),
            'edit'   => EditSection::route('/{record}/edit'),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return route('filament.admin.pages.sections');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
