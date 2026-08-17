<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Domains\Courses\Models\Section as CourseSection;
use App\Filament\Resources\Lessons\LessonResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    protected string $view = 'filament.resources.lessons.create-lesson';

    protected function getHeaderActions(): array
    {
        return [];
    }

    // ── Main form: basic info + settings only ───────────────────
    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Basic')
                    ->schema([
                        Select::make('section_id')
                            ->label('Section')
                            ->options(
                                CourseSection::with('course')->get()
                                    ->mapWithKeys(fn ($s) => [$s->id => ($s->course?->title ?? '?').' — '.$s->title])
                            )
                            ->searchable()
                            ->required(),
                        TextInput::make('title')->required()->maxLength(255),
                        Select::make('type')
                            ->options([
                                'video'   => 'Video',
                                'article' => 'Article',
                                'file'    => 'File / Document',
                            ])
                            ->default('video')
                            ->live()
                            ->required(),
                        Textarea::make('description')->rows(3)->columnSpanFull(),
                        TextInput::make('duration')
                            ->numeric()
                            ->suffix('minutes')
                            ->dehydrateStateUsing(fn (mixed $state) => filled($state) ? (int) round(((float) $state) * 60) : null)
                            ->hidden(fn (Get $get): bool => $get('type') === 'article')
                            ->default(null),
                        TextInput::make('order')->numeric()->default(1),
                        Toggle::make('is_preview')->default(false),
                    ]),
            ]);
    }

    // ── Video sub-form ──────────────────────────────────────────
    public function videoForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('video_path')
                    ->label('Video File')
                    ->disk('r2-private')
                    ->visibility('private')
                    ->directory('lessons/videos')
                    ->acceptedFileTypes(['video/*'])
                    ->maxSize(512 * 1024),
                TextInput::make('video_url')
                    ->label('External Video URL')
                    ->url()
                    ->placeholder('https://youtube.com/watch?v=...'),
                Select::make('video_provider')
                    ->label('Video Provider')
                    ->options(['youtube'=>'YouTube','vimeo'=>'Vimeo','other'=>'Other'])
                    ->placeholder('Select provider'),
            ]);
    }

    // ── Article sub-form ────────────────────────────────────────
    public function articleForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                RichEditor::make('content')->columnSpanFull(),
            ]);
    }

    // ── File sub-form ───────────────────────────────────────────
    public function fileForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('attachment')
                    ->label('Document File')
                    ->disk('r2-private')
                    ->visibility('private')
                    ->directory('lessons/documents')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(50 * 1024)
                    ->columnSpanFull(),
                TextInput::make('attachment_name')
                    ->label('Display Name')
                    ->placeholder('e.g. Lecture Slides, Course Notes...')
                    ->maxLength(255),
            ]);
    }

    // ── Attachment sub-form (video / article) ───────────────────
    public function attachmentForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                FileUpload::make('attachment')
                    ->disk('r2-private')
                    ->visibility('private')
                    ->directory('lessons/attachments')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(50 * 1024),
                TextInput::make('attachment_name')
                    ->label('Attachment Display Name')
                    ->placeholder('e.g. Course Notes, Cheat Sheet...')
                    ->maxLength(255),
            ]);
    }

    protected function getForms(): array
    {
        return ['form', 'videoForm', 'articleForm', 'fileForm', 'attachmentForm'];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $type = $data['type'] ?? 'video';

        // Merge sub-form states (all share statePath('data') so they may overlap;
        // explicit merge ensures latest values are used)
        foreach (['videoForm', 'articleForm', 'fileForm', 'attachmentForm'] as $f) {
            $data = array_merge($data, $this->{$f}->getState());
        }

        // Null out irrelevant content per type
        if ($type !== 'video') {
            $data['video_path']     = null;
            $data['video_url']      = null;
            $data['video_provider'] = null;
        }
        if ($type === 'article') {
            $data['duration'] = null;
        }
        if ($type === 'video' || $type === 'article') {
            // attachment is allowed for video/article
        } elseif ($type === 'file') {
            // attachment is the file itself — keep it
        } else {
            $data['attachment']      = null;
            $data['attachment_name'] = null;
        }

        return $data;
    }

    protected function getViewData(): array
    {
        return [
            'backUrl'  => route('filament.admin.pages.lessons'),
            'sections' => CourseSection::with('course')
                ->get()
                ->mapWithKeys(fn ($s) => [$s->id => ($s->course?->title ?? '?').' — '.$s->title]),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.admin.pages.lessons');
    }
}
