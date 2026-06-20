<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record && in_array($record->name, RoleResource::protectedRoleNames(), true))
                            ->helperText(fn ($record) => $record && in_array($record->name, RoleResource::protectedRoleNames(), true)
                                ? 'System role names are locked — the platform depends on this exact name.'
                                : 'Lowercase, hyphen-separated, e.g. "content-manager".'),
                    ]),

                Section::make('Permissions')
                    ->description('Toggle every action this role is allowed to perform. Options are grouped by module.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options(fn () => Permission::all()
                                ->sortBy('name')
                                ->mapWithKeys(fn (Permission $permission) => [
                                    $permission->id => str($permission->name)->replace('.', ' · ')->headline(),
                                ])
                                ->all())
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3),
                    ]),
            ]);
    }
}
