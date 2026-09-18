<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Filament\Resources\Support\ContentActions;
use App\Filament\Resources\Support\ContentForms;
use App\Models\Skill;
use App\Services\Admin\Content\ProfileContentService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static \UnitEnum|string|null $navigationGroup = 'Profile';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            ContentForms::localizedTabs(['name']),
            Section::make('Display')
                ->schema([
                    TextInput::make('group')->maxLength(255),
                    TextInput::make('level')->numeric()->minValue(0)->maxValue(100),
                    Toggle::make('is_active')->default(true),
                    TextInput::make('sort_order')->numeric()->default(0)->required(),
                ])
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name.en')->label('Name')->searchable(),
                TextColumn::make('group')->searchable()->sortable(),
                TextColumn::make('level')->numeric()->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('sort_order')->numeric()->sortable(),
            ])
            ->recordActions([
                ContentActions::edit(ProfileContentService::class),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSkills::route('/')];
    }
}
