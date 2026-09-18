<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Filament\Resources\Support\ContentActions;
use App\Filament\Resources\Support\ContentForms;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use App\Services\Admin\Content\ProjectContentService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Projects';

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                ContentForms::localizedTabs([
                    'title',
                    'slug',
                    'summary',
                    'body',
                    'role',
                    'duration',
                    'industry',
                    'challenge',
                    'solution',
                    'features',
                    'development_challenges',
                    'results',
                    'metrics',
                ]),
                Section::make('Classification')
                    ->schema([
                        Select::make('project_category_id')
                            ->label('Category')
                            ->options(fn (): array => ProjectCategory::query()->ordered()->get()->mapWithKeys(
                                fn (ProjectCategory $category): array => [$category->id => $category->localized('name', 'en')],
                            )->all())
                            ->searchable(),
                        Select::make('technologies')
                            ->label('Technologies')
                            ->options(fn (): array => Technology::query()->ordered()->pluck('name', 'id')->all())
                            ->multiple()
                            ->preload(),
                    ])
                    ->columns(2),
                ContentForms::imageUpload('cover_image_path', 'projects/covers')
                    ->label('Cover image'),
                ContentForms::publicationSection(),
                ContentForms::seoSection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['category'])->withCount(['media', 'views', 'likes']))
            ->columns([
                ImageColumn::make('cover_image_path')->disk((string) config('portfolio.media.disk', 'public')),
                TextColumn::make('title.en')->label('Title')->searchable(),
                TextColumn::make('category.name.en')->label('Category')->sortable(),
                TextColumn::make('status')->badge()->searchable(),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('media_count')->label('Media')->numeric()->sortable(),
                TextColumn::make('views_count')->label('Views')->numeric()->sortable(),
                TextColumn::make('likes_count')->label('Likes')->numeric()->sortable(),
                TextColumn::make('sort_order')->numeric()->sortable(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(\App\Enums\PublicationStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ContentActions::edit(ProjectContentService::class),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProjects::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
