<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ViewRole;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Roles';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateRole::route('/create'),
            'view'   => ViewRole::route('/{record}'),
            'edit'   => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return route('filament.admin.pages.roles');
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('roles.view');
    }

    public static function canCreate(): bool
    {
        return PanelAccess::can('roles.create');
    }

    public static function canEdit($record): bool
    {
        return PanelAccess::can('roles.update');
    }

    public static function canDelete($record): bool
    {
        return PanelAccess::can('roles.delete')
            && !in_array($record->name, self::undeletableRoleNames(), true);
    }

    /**
     * The 9 roles the platform seeds and depends on — their name can't be changed
     * and they're flagged "System" in the UI, but (aside from super-admin) they can
     * still be deleted.
     */
    public static function protectedRoleNames(): array
    {
        return [
            'super-admin', 'admin', 'finance-manager', 'accountant',
            'content-manager', 'moderator', 'support-staff', 'instructor', 'student',
        ];
    }

    /**
     * Roles that can never be deleted, regardless of the roles.delete permission.
     */
    public static function undeletableRoleNames(): array
    {
        return ['super-admin'];
    }
}
