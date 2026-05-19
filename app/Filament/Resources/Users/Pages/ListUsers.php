<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    #[Url(as: 'role')]
    public ?string $role = null;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if (in_array($this->role, ['student', 'instructor'], true)) {
            $query->where('role', $this->role);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
