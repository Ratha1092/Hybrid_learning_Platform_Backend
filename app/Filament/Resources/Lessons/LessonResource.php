<?php

namespace App\Filament\Resources\Lessons;

use App\Domains\Courses\Models\Lesson;

use App\Filament\Resources\Lessons\Pages\CreateLesson;
use App\Filament\Resources\Lessons\Pages\EditLesson;
use App\Filament\Resources\Lessons\Pages\ListLessons;
use App\Filament\Resources\Lessons\Pages\ViewLesson;

use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Resources\Lessons\Schemas\LessonInfolist;

use App\Filament\Resources\Lessons\Tables\LessonsTable;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;
    protected static string|BackedEnum|null $navigationIcon =Heroicon::OutlinedPlayCircle;
    protected static ?string $navigationLabel = 'Lessons';
    protected static ?string $modelLabel = 'Lesson';
    protected static ?string $pluralModelLabel = 'Lessons';
    protected static string|\UnitEnum|null $navigationGroup ='Course Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute ='title';
    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LessonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => ListLessons::route('/'),
            'create' => CreateLesson::route('/create'),
            'view' => ViewLesson::route('/{record}'),
            'edit' => EditLesson::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}