<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Domains\Courses\Models\Section as CourseSection;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Lesson title, section assignment and type')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Select::make('section_id')
                            ->label('Section')
                            ->options(
                                CourseSection::query()->pluck('title', 'id')
                            )
                            ->searchable()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->options([
                                'video'      => 'Video',
                                'article'    => 'Article',
                                'quiz'       => 'Quiz',
                                'live'       => 'Live',
                                'assignment' => 'Assignment',
                            ])
                            ->default('video')
                            ->required(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Content')
                    ->description('Rich text content for article or assignment lessons')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        RichEditor::make('content')
                            ->columnSpanFull(),
                    ]),

                Section::make('Video')
                    ->description('Upload a video file or link to YouTube, Vimeo, etc.')
                    ->icon('heroicon-o-play-circle')
                    ->schema([
                        FileUpload::make('video_path')
                            ->label('Video File')
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

                Section::make('Attachment')
                    ->description('Optional downloadable file for students')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        FileUpload::make('attachment')
                            ->directory('lessons/attachments'),
                        TextInput::make('attachment_name')
                            ->label('Attachment Display Name')
                            ->placeholder('e.g. Course Notes, Cheat Sheet...')
                            ->maxLength(255),
                    ])
                    ->columns(2),

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
