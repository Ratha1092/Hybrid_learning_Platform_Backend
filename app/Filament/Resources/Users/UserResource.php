<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Domains\Users\Models\User;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use function Filament\Support\original_request;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Users';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getNavigationItems(): array
    {
        $activeRoutePattern = static::getNavigationItemActiveRoutePattern();
        $indexUrl = static::getUrl();

        return [
            NavigationItem::make('Users')
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->extraAttributes(['class' => 'lh-users-root'])
                ->isActiveWhen(fn (): bool => true)
                ->sort(static::getNavigationSort())
                ->childItems([
                    NavigationItem::make('All Users')
                        ->isActiveWhen(fn (): bool => original_request()->routeIs($activeRoutePattern) && ! in_array(original_request()->query('role'), ['student', 'instructor'], true))
                        ->url($indexUrl),
                    NavigationItem::make('Students')
                        ->isActiveWhen(fn (): bool => original_request()->routeIs($activeRoutePattern) && original_request()->query('role') === 'student')
                        ->url(static::getUrl(parameters: ['role' => 'student'])),
                    NavigationItem::make('Instructors')
                        ->isActiveWhen(fn (): bool => original_request()->routeIs($activeRoutePattern) && original_request()->query('role') === 'instructor')
                        ->url(static::getUrl(parameters: ['role' => 'instructor'])),
                ]),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
