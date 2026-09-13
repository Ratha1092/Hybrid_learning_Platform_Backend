<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Domains\Courses\Models\Section as CourseSection;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Basic Info ────────────────────────────────────────────
                Section::make('Basic Information')
                    ->description('Lesson title, section assignment and type')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Select::make('section_id')
                            ->label('Section')
                            ->options(CourseSection::query()->pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->options([
                                'video'   => 'Video',
                                'article' => 'Article',
                                'file'    => 'File / Document',
                            ])
                            ->default('video')
                            ->required()
                            ->live(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                // ── VIDEO ─────────────────────────────────────────────────
                Section::make('Video')
                    ->description('Upload a video file or link to YouTube, Vimeo, etc.')
                    ->icon('heroicon-o-play-circle')
                    ->hidden(fn (Get $get): bool => $get('type') !== 'video')
                    ->schema([
                        FileUpload::make('video_path')
                            ->label('Video File')
                            ->disk('r2-private')
                            ->visibility('private')
                            ->directory('lessons/videos')
                            ->acceptedFileTypes(['video/*'])
                            ->maxSize(512 * 1024)
                            ->columnSpanFull(),
                        TextInput::make('video_url')
                            ->label('External Video URL')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...'),
                        Select::make('video_provider')
                            ->label('Video Provider')
                            ->options([
                                'youtube' => 'YouTube',
                                'vimeo'   => 'Vimeo',
                                'other'   => 'Other',
                            ])
                            ->placeholder('Select provider'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ── ARTICLE ───────────────────────────────────────────────
                Section::make('Content')
                    ->description('Write the article content for this lesson')
                    ->icon('heroicon-o-pencil-square')
                    ->hidden(fn (Get $get): bool => $get('type') !== 'article')
                    ->schema([
                        RichEditor::make('content')
                            ->columnSpanFull(),
                    ]),


                Section::make('Lesson Builder')
                    ->description('Structure the teaching flow inside this lesson')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Repeater::make('objectives')
                            ->relationship('objectives')
                            ->label('Learning Objectives')
                            ->simple(
                                TextInput::make('objective')
                                    ->required()
                                    ->maxLength(500)
                            )
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('order')
                            ->addActionLabel('Add objective')
                            ->columnSpanFull(),
                        Repeater::make('content_blocks')
                            ->relationship('contentBlocks')
                            ->label('Content Blocks')
                            ->schema([
                                Select::make('type')
                                    ->options([
                                        'text' => 'Text',
                                        'video' => 'Video',
                                        'image' => 'Image',
                                        'code' => 'Code',
                                        'resource' => 'Resource',
                                        'external' => 'External Link',
                                    ])
                                    ->required()
                                    ->live(),
                                TextInput::make('title')->maxLength(255),
                                RichEditor::make('content')
                                    ->hidden(fn (Get $get): bool => $get('type') === 'external')
                                    ->columnSpanFull(),
                                FileUpload::make('media_path')
                                    ->label('Media File')
                                    ->disk('r2-private')
                                    ->visibility('private')
                                    ->directory('lessons/content')
                                    ->hidden(fn (Get $get): bool => !in_array($get('type'), ['video', 'image', 'resource'])) ,
                                TextInput::make('media_url')
                                    ->label('External URL')
                                    ->url()
                                    ->hidden(fn (Get $get): bool => $get('type') !== 'external'),
                                TextInput::make('language')
                                    ->placeholder('e.g. javascript')
                                    ->hidden(fn (Get $get): bool => $get('type') !== 'code'),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('order')
                            ->addActionLabel('Add content block')
                            ->columnSpanFull(),
                        Repeater::make('takeaways')
                            ->relationship('takeaways')
                            ->label('Key Takeaways')
                            ->simple(
                                TextInput::make('takeaway')
                                    ->required()
                                    ->maxLength(500)
                            )
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('order')
                            ->addActionLabel('Add takeaway')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Completion Requirements')
                    ->description('Define what a student must complete for this lesson')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Group::make()
                            ->relationship('completionRule')
                            ->schema([
                                Toggle::make('watch_video')->label('Watch video'),
                                Toggle::make('read_content')->label('Read lesson content'),
                                Toggle::make('pass_quiz')->label('Pass knowledge check'),
                                Toggle::make('submit_assignment')->label('Submit assignment'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),

                Section::make('Knowledge Check')
                            ->description('Add an optional quiz to reinforce the lesson')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema([
                                Repeater::make('assessments')
                                    ->relationship('assessments')
                                    ->schema([
                                        TextInput::make('title')->required()->maxLength(255),
                                        Textarea::make('description')->rows(2),
                                        TextInput::make('passing_score')->numeric()->suffix('%'),
                                        TextInput::make('attempts')->numeric()->minValue(1),
                                        Toggle::make('is_required')->label('Required'),
                                        Repeater::make('questions')
                                            ->relationship('questions')
                                            ->schema([
                                                Textarea::make('question')->required()->rows(2)->columnSpanFull(),
                                                Select::make('type')
                                                    ->options([
                                                        'single_choice' => 'Single choice',
                                                        'multiple_choice' => 'Multiple choice',
                                                        'true_false' => 'True / False',
                                                    ])
                                                    ->default('single_choice')
                                                    ->required(),
                                                TagsInput::make('options')
                                                    ->label('Answer options')
                                                    ->placeholder('Add an option'),
                                                TagsInput::make('correct_options')
                                                    ->label('Correct answer(s)')
                                                    ->placeholder('Add the exact correct answer'),
                                                Textarea::make('explanation')->rows(2),
                                                TextInput::make('points')->numeric()->default(1)->minValue(1),
                                            ])
                                            ->defaultItems(0)
                                            ->reorderable()
                                            ->orderColumn('order')
                                            ->addActionLabel('Add question')
                                            ->columnSpanFull(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add knowledge check')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),

                    Section::make('Practical Activity')
                            ->description('Give students an assignment to complete and submit')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Repeater::make('assignments')
                                    ->relationship('assignments')
                                    ->schema([
                                        TextInput::make('title')->required()->maxLength(255),
                                        RichEditor::make('instructions')->required()->columnSpanFull(),
                                        Select::make('submission_type')
                                            ->options([
                                                'file' => 'File submission',
                                                'text' => 'Text response',
                                                'url' => 'URL submission',
                                            ])
                                            ->default('file')
                                            ->required(),
                                        TextInput::make('max_score')->numeric()->minValue(1),
                                        Toggle::make('is_required')->label('Required'),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add assignment')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),

                    Section::make('Resources')
                            ->description('Add downloadable files students can reference')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->schema([
                                Repeater::make('attachments')
                                    ->relationship('attachments')
                                    ->schema([
                                        TextInput::make('title')->required()->maxLength(255),
                                        Select::make('type')->options([
                                            'pdf' => 'PDF',
                                            'slides' => 'Slides',
                                            'source_code' => 'Source code',
                                            'template' => 'Template',
                                            'other' => 'Other',
                                        ]),
                                        FileUpload::make('file_path')
                                            ->disk('r2-private')
                                            ->visibility('private')
                                            ->directory('lessons/resources')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Add resource')
                                    ->columnSpanFull(),
                                ])
                                ->columnSpanFull(),
                // ── FILE / DOCUMENT ───────────────────────────────────────
                Section::make('File / Document')
                    ->description('Upload a PDF, PowerPoint, Word, or other document for students')
                    ->icon('heroicon-o-paper-clip')
                    ->hidden(fn (Get $get): bool => $get('type') !== 'file')
                    ->schema([
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ── ATTACHMENT: video + article optional download ─────────
                Section::make('Attachment')
                    ->description('Optional downloadable file for students')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->hidden(fn (Get $get): bool => !in_array($get('type'), ['video', 'article']))
                    ->schema([
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // ── SETTINGS ──────────────────────────────────────────────
                Section::make('Settings')
                    ->description('Duration, ordering and preview access')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        TextInput::make('duration')
                            ->numeric()
                            ->suffix('minutes')
                            ->afterStateHydrated(fn (TextInput $component, mixed $state) => $component->state(filled($state) ? round(((float) $state) / 60) : null))
                            ->dehydrateStateUsing(fn (mixed $state) => filled($state) ? (int) round(((float) $state) * 60) : null)
                            ->hidden(fn (Get $get): bool => $get('type') === 'article'),
                        TextInput::make('order')
                            ->numeric()
                            ->default(1),
                        Toggle::make('is_preview')
                            ->label('Free Preview')
                            ->inline(false),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
