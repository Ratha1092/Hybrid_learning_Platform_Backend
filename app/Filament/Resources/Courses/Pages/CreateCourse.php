<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domains\Courses\Models\Category;
use App\Domains\Courses\Models\Section as SectionModel;
use App\Domains\Users\Models\User;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected string $view = 'filament.resources.courses.create-course';

    public array $selectedSectionIds = [];

    public function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('short_description')->rows(3)->columnSpanFull(),
            ]),
            Section::make()->columns(3)->schema([
                Select::make('instructor_id')
                    ->label('Instructor')
                    ->options(fn () => User::role('instructor')->orderBy('name')->pluck('name', 'id'))
                    ->required(),
                Select::make('category_id')
                    ->label('Category')
                    ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                    ->required(),
                TextInput::make('price')->numeric()->prefix('$')->required(),
                TextInput::make('duration')->numeric()->suffix('minutes'),
                Select::make('level')
                    ->options(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'])
                    ->required(),
                TextInput::make('language')->default('English')->required(),
            ]),
            Section::make()->columns(2)->schema([
                Textarea::make('requirements')->rows(5),
                Textarea::make('what_you_will_learn')->rows(5),
            ]),
            Section::make()->columns(2)->schema([
                Select::make('status')
                    ->options(['draft' => 'Draft', 'pending_review' => 'Pending Review', 'published' => 'Published', 'rejected' => 'Rejected', 'archived' => 'Archived'])
                    ->required(),
                Select::make('visibility')
                    ->options(['public' => 'Public', 'private' => 'Private', 'unlisted' => 'Unlisted'])
                    ->default('public')
                    ->required(),
                Toggle::make('is_published')->label('Published'),
            ]),
        ])->statePath('data');
    }

    public function thumbnailForm(Schema $form): Schema
    {
        return $form->components([
            FileUpload::make('thumbnail')
                ->image()
                ->disk('r2')
                ->directory('courses/thumbnails'),
        ])->statePath('data');
    }

    public function descriptionForm(Schema $form): Schema
    {
        return $form->components([
            RichEditor::make('description'),
        ])->statePath('data');
    }

    protected function getForms(): array
    {
        return ['form', 'thumbnailForm', 'descriptionForm'];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $thumbState = $this->thumbnailForm->getState();
        $data['thumbnail'] = $thumbState['thumbnail'] ?? null;

        $descState = $this->descriptionForm->getState();
        $data['description'] = $descState['description'] ?? null;

        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->selectedSectionIds)) {
            SectionModel::whereIn('id', $this->selectedSectionIds)
                ->whereNull('course_id')
                ->update(['course_id' => $this->record->id]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'backUrl'             => url('/admin/courses'),
            'instructors'         => User::role('instructor')->orderBy('name')->pluck('name', 'id'),
            'categories'          => Category::orderBy('name')->pluck('name', 'id'),
            'unattachedSections'  => SectionModel::whereNull('course_id')
                ->withCount('lessons')
                ->orderBy('title')
                ->get(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return url('/admin/courses/' . $this->record->id);
    }
}
