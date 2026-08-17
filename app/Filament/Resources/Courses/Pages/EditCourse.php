<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domains\Courses\Models\Category;
use App\Domains\Users\Models\User;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;
    protected string $view = 'filament.resources.courses.edit-course';
    public ?array $thumbnailData = [];

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
                    ->required(),
                Toggle::make('is_published')->label('Published'),
                Toggle::make('certificate_enabled')->label('Enable Certificate'),
                TextInput::make('commission_percentage')->numeric()->suffix('%')->default(20)->columnSpanFull(),
                Textarea::make('rejection_reason')->rows(3)->columnSpanFull(),
            ]),
        ])->statePath('data');
    }

    // Sub-form for thumbnail FileUpload
    public function thumbnailForm(Schema $form): Schema
    {
        return $form->components([
            FileUpload::make('thumbnail')
                ->image()
                ->previewable(false)
                ->disk('r2')
                ->directory('courses/thumbnails'),
        ])->statePath('thumbnailData');
    }

    // Sub-form for description RichEditor
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
    protected function fillForm(): void
    {
        parent::fillForm();

        $this->thumbnailForm->fill(['thumbnail' => $this->getRecord()->thumbnail]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $thumbState = $this->thumbnailForm->getState();
        $data['thumbnail'] = $thumbState['thumbnail'] ?? null;

        // Read directly from shared Livewire state — avoids triggering RichEditor's internal type check
        $data['description'] = $this->data['description'] ?? null;

        // Only super-admin may change the price — restore original for everyone else
        if (! auth()->user()?->isAdmin()) {
            $data['price'] = $this->record->price;
        }

        return $data;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return url('/admin/courses');
    }

    protected function getViewData(): array
    {
        $record = $this->record;

        return [
            'backUrl'         => url('/admin/courses'),
            'enrollmentCount' => $record->enrollments()->count(),
            'reviewCount'     => $record->reviews()->count(),
            'avgRating'       => round($record->reviews()->avg('rating') ?? 0, 1),
            'instructors'     => User::role('instructor')->orderBy('name')->pluck('name', 'id'),
            'categories'      => Category::orderBy('name')->pluck('name', 'id'),
            'canEditPrice'    => auth()->user()?->isAdmin() ?? false,
        ];
    }
}
