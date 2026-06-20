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
                    User::role('student')->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->role('student')
                ),

            'instructors' => Tab::make('Instructors')
                ->badge(
                    User::role('instructor')->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->role('instructor')
                ),

            'admins' => Tab::make('Admins')
                ->badge(
                    User::role(['super-admin', 'admin'])->count()
                )
                ->modifyQueryUsing(fn ($query) =>
                    $query->role(['super-admin', 'admin'])
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