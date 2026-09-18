<?php

namespace App\Filament\Resources\ProjectMedia;

use App\Filament\Resources\ProjectMedia\Pages\ManageProjectMedia;
use App\Filament\Resources\Support\ContentActions;
use App\Filament\Resources\Support\ContentForms;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Services\Admin\Content\ProjectMediaContentService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectMediaResource extends Resource
{
    protected static ?string $model = ProjectMedia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Project media';

    protected static \UnitEnum|string|null $navigationGroup = 'Media';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Media')
                ->schema([
                    Select::make('project_id')
                        ->label('Project')
                        ->required()
                        ->options(fn (): array => Project::query()->ordered()->get()->mapWithKeys(
                            fn (Project $project): array => [$project->id => $project->localized('title', 'en')],
                        )->all())
                        ->searchable(),
                    FileUpload::make('path')
                        ->label('Image or video')
                        ->required()
                        ->disk((string) config('portfolio.media.disk', 'public'))
                        ->directory('projects/media')
                        ->visibility('public')
                        ->acceptedFileTypes([
                            ...config('portfolio.media.image_mimes', []),
                            ...config('portfolio.media.video_mimes', []),
                        ])
                        ->maxSize((int) config('portfolio.media.max_video_kb', 51200))
                        ->previewable()
                        ->downloadable()
                        ->openable(),
                    ContentForms::imageUpload('poster_path', 'projects/posters')
                        ->label('Video poster image'),
                    TextInput::make('sort_order')->numeric()->default(0)->required(),
                ])
                ->columns(2),
            ContentForms::localizedTabs(['caption', 'alt_text']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                ImageColumn::make('path')->disk((string) config('portfolio.media.disk', 'public')),
                TextColumn::make('project.title.en')->label('Project')->searchable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('caption.en')->label('Caption')->searchable(),
                TextColumn::make('file_size')->label('Bytes')->numeric()->sortable(),
                TextColumn::make('sort_order')->numeric()->sortable(),
            ])
            ->recordActions([
                ContentActions::edit(ProjectMediaContentService::class),
                DeleteAction::make()
                    ->using(fn (ProjectMedia $record) => app(ProjectMediaContentService::class)->delete($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageProjectMedia::route('/')];
    }
}
