<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Domains\Courses\Models\Category;
use App\Domains\Users\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('short_description')
                            ->rows(3),
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->directory('courses/thumbnails')
                            ->imageEditor(),
                        TextInput::make('preview_video_url')
                            ->label('Preview Video URL')
                            ->url()
                            ->placeholder(
                                'https://youtube.com/...'
                            ),
                    ])
                    ->columns(2),
                Section::make('Course Details')

                    ->schema([
                        Select::make('instructor_id')
                            ->label('Instructor')
                            ->relationship('instructor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        TextInput::make('duration')
                            ->numeric()
                            ->suffix('minutes'),

                        Select::make('level')

                            ->options([
                                'beginner' => 'Beginner',
                                'intermediate' => 'Intermediate',
                                'advanced' => 'Advanced',
                            ])
                            ->required(),
                        TextInput::make('language')
                            ->default('English')
                            ->required(),
                    ])
                    ->columns(3),
                Section::make('Learning Content')
                    ->schema([
                        Textarea::make('requirements')
                            ->rows(5),
                        Textarea::make('what_you_will_learn')
                            ->rows(5),
                    ])

                    ->columns(2),
                Section::make('Publishing')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending_review' => 'Pending Review',
                                'published' => 'Published',
                                'rejected' => 'Rejected',
                                'archived' => 'Archived',
                            ])
                            ->required(),
                        Select::make('visibility')
                            ->options([
                                'public' => 'Public',
                                'private' => 'Private',
                                'unlisted' => 'Unlisted',
                            ])
                            ->default('public')
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Published'),

                        Toggle::make('certificate_enabled')
                            ->label('Enable Certificate'),
                    ])
                    ->columns(2),
                Section::make('Platform Settings')
                    ->schema([
                        TextInput::make('commission_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->default(20),
                    ]),
            ]);
    }
}