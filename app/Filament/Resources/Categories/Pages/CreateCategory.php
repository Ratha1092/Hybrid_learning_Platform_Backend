<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected string $view = 'filament.resources.categories.create-category';

    public function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description'),
                TextInput::make('icon')->maxLength(100),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_featured')->label('Featured'),
            ]),
        ])->statePath('data');
    }

    public function imageForm(Schema $form): Schema
    {
        return $form->components([
            FileUpload::make('image')
                ->image()
                ->directory('categories'),
        ])->statePath('data');
    }

    protected function getForms(): array
    {
        return ['form', 'imageForm'];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $imageState = $this->imageForm->getState();
        $data['image'] = $imageState['image'] ?? null;
        return $data;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.categories');
    }

    protected function getViewData(): array
    {
        return [
            'backUrl' => route('filament.admin.pages.categories'),
        ];
    }
}
