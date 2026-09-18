<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Models\ContactMessage;
use App\Services\Admin\Content\ContactInboxService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Contact inbox';

    protected static \UnitEnum|string|null $navigationGroup = 'Engagement';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Private message')
                    ->schema([
                        TextInput::make('name')->disabled()->dehydrated(false),
                        TextInput::make('email')->disabled()->dehydrated(false),
                        TextInput::make('phone')->disabled()->dehydrated(false),
                        TextInput::make('company')->disabled()->dehydrated(false),
                        TextInput::make('project_type')->disabled()->dehydrated(false),
                        TextInput::make('budget_range')->disabled()->dehydrated(false),
                        Textarea::make('message')->disabled()->dehydrated(false)->rows(6)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Inbox workflow')
                    ->schema([
                        Select::make('status')->options(ContactMessageStatus::class)->required(),
                        Textarea::make('admin_notes')->rows(6)->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')->label('Private email'),
                TextEntry::make('phone'),
                TextEntry::make('company'),
                TextEntry::make('project_type'),
                TextEntry::make('budget_range'),
                TextEntry::make('message')->columnSpanFull(),
                TextEntry::make('status')->badge(),
                TextEntry::make('admin_notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->label('Private email')->searchable(),
                TextColumn::make('project_type')->searchable(),
                TextColumn::make('budget_range')->searchable(),
                TextColumn::make('status')->badge()->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ContactMessageStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('markRead')
                    ->label('Mark read')
                    ->icon(Heroicon::OutlinedEnvelopeOpen)
                    ->visible(fn (ContactMessage $record): bool => $record->status === ContactMessageStatus::New)
                    ->action(fn (ContactMessage $record): ContactMessage => app(ContactInboxService::class)->markRead($record)),
                EditAction::make()
                    ->using(fn (Model $record, array $data): Model => app(ContactInboxService::class)->update($record, $data)),
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
            'index' => ManageContactMessages::route('/'),
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
