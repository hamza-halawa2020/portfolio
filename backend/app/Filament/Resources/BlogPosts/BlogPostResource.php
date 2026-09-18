<?php

namespace App\Filament\Resources\BlogPosts;

use App\Enums\PublicationStatus;
use App\Filament\Resources\BlogPosts\Pages\ManageBlogPosts;
use App\Filament\Resources\Support\ContentActions;
use App\Filament\Resources\Support\ContentForms;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use App\Services\Admin\Content\BlogPostContentService;
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

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Blog posts';

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                ContentForms::localizedTabs(['title', 'slug', 'excerpt', 'body']),
                Section::make('Classification')
                    ->schema([
                        Select::make('blog_category_id')
                            ->label('Category')
                            ->options(fn (): array => BlogCategory::query()->ordered()->get()->mapWithKeys(
                                fn (BlogCategory $category): array => [$category->id => $category->localized('name', 'en')],
                            )->all())
                            ->searchable(),
                        Select::make('tags')
                            ->label('Tags')
                            ->options(fn (): array => Tag::query()->get()->mapWithKeys(
                                fn (Tag $tag): array => [$tag->id => $tag->localized('name', 'en')],
                            )->all())
                            ->multiple()
                            ->preload(),
                    ])
                    ->columns(2),
                ContentForms::imageUpload('cover_image_path', 'blog/covers')
                    ->label('Cover image'),
                ContentForms::publicationSection(),
                ContentForms::seoSection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('category'))
            ->columns([
                ImageColumn::make('cover_image_path')->disk((string) config('portfolio.media.disk', 'public')),
                TextColumn::make('title.en')->label('Title')->searchable(),
                TextColumn::make('category.name.en')->label('Category')->sortable(),
                TextColumn::make('status')->badge()->searchable(),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('reading_time_minutes')->label('Read min')->numeric()->sortable(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(PublicationStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ContentActions::edit(BlogPostContentService::class),
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
            'index' => ManageBlogPosts::route('/'),
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
