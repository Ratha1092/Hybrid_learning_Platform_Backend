<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domains\Users\Models\User;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->badge(User::count()),
            'students' => Tab::make('Students')
                ->badge(
                    User::where('role', 'student')->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->where('role', 'student')
                ),

            'instructors' => Tab::make('Instructors')
                ->badge(
                    User::where('role', 'instructor')->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->where('role', 'instructor')
                ),

            'admins' => Tab::make('Admins')
                ->badge(
                    User::where('role', 'admin')->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->where('role', 'admin')
                ),

            'suspended' => Tab::make('Suspended')
                ->badge(
                    User::where('status', 'suspended')->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->where('status', 'suspended')
                ),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}