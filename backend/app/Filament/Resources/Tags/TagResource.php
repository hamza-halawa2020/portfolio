<?php

namespace App\Filament\Resources\Tags;

use App\Filament\Resources\Support\ContentActions;
use App\Filament\Resources\Support\ContentForms;
use App\Filament\Resources\Tags\Pages\ManageTags;
use App\Models\Tag;
use App\Services\Admin\Content\LocalizedRecordService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Tags';

    protected static \UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            ContentForms::localizedTabs(['name', 'slug']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name.en')->label('Name')->searchable(),
                TextColumn::make('slug.en')->label('English slug')->searchable(),
            ])
            ->recordActions([
                ContentActions::edit(LocalizedRecordService::class),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTags::route('/')];
    }
}
