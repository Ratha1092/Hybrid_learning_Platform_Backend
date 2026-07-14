<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Domains\Courses\Models\Section as CourseSection;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                                'quiz'    => 'Quiz',
                                'file'    => 'File / Document',
                            ])
                            ->default('video')
                            ->required()
                            ->live(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

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
                    ->columns(2),

                // ── ARTICLE ───────────────────────────────────────────────
                Section::make('Content')
                    ->description('Write the article content for this lesson')
                    ->icon('heroicon-o-pencil-square')
                    ->hidden(fn (Get $get): bool => $get('type') !== 'article')
                    ->schema([
                        RichEditor::make('content')
                            ->columnSpanFull(),
                    ]),

                // ── QUIZ ──────────────────────────────────────────────────
                Section::make('Quiz Questions')
                    ->description('Add multiple-choice questions for this quiz')
                    ->icon('heroicon-o-question-mark-circle')
                    ->hidden(fn (Get $get): bool => $get('type') !== 'quiz')
                    ->schema([
                        Repeater::make('quiz_data')
                            ->label('')
                            ->schema([
                                TextInput::make('question')
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('option_a')->label('Option A')->required(),
                                TextInput::make('option_b')->label('Option B')->required(),
                                TextInput::make('option_c')->label('Option C'),
                                TextInput::make('option_d')->label('Option D'),
                                Select::make('correct')
                                    ->label('Correct Answer')
                                    ->options([
                                        'a' => 'Option A',
                                        'b' => 'Option B',
                                        'c' => 'Option C',
                                        'd' => 'Option D',
                                    ])
                                    ->required(),
                                Textarea::make('explanation')
                                    ->label('Explanation (optional)')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Question')
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

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
                    ->columns(2),

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
                    ->columns(2),

                // ── SETTINGS ──────────────────────────────────────────────
                Section::make('Settings')
                    ->description('Duration, ordering and preview access')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        TextInput::make('duration')
                            ->numeric()
                            ->suffix('minutes'),
                        TextInput::make('order')
                            ->numeric()
                            ->default(1),
                        Toggle::make('is_preview')
                            ->label('Free Preview')
                            ->inline(false),
                    ])
                    ->columns(3),
            ]);
    }
}
