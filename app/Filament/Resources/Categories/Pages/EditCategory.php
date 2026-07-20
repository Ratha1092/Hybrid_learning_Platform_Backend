<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
    protected string $view = 'filament.resources.categories.edit-category';

    // Main form — all fields EXCEPT image (handled by imageForm)
    public function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        if (! $record) {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')->columnSpanFull(),
                TextInput::make('icon')->maxLength(100),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_featured')->label('Featured')->columnSpanFull(),
            ]),
        ])->statePath('data');
    }

    // Separate form for image upload only — kept on its own state path so it
    // never shares Livewire state with `form` (or the raw wire:model="data.*"
    // inputs in the view), which was clobbering the FileUpload's array state
    // back to a plain string and crashing getUploadedFiles().
    public function imageForm(Schema $form): Schema
    {
        return $form->components([
            FileUpload::make('image')
                ->image()
                ->directory('categories'),
        ])->statePath('imageData');
    }

    protected function getForms(): array
    {
        return ['form', 'imageForm'];
    }

    // Process imageForm file upload and merge image into saved data
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $imageState = $this->imageForm->getState();
        $data['image'] = $imageState['image'] ?? null;
        return $data;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.categories');
    }

    protected function getViewData(): array
    {
        return [
            'backUrl'     => route('filament.admin.pages.categories'),
            'courseCount' => $this->record->courses()->count(),
        ];
    }
}
