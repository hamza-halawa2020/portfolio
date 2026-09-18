<?php

namespace App\Filament\Resources\Testimonials;

use App\Enums\TestimonialStatus;
use App\Filament\Resources\Support\ContentForms;
use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\Admin\Content\TestimonialModerationService;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Testimonials';

    protected static \UnitEnum|string|null $navigationGroup = 'Engagement';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Private submitter details')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('contact_email')->label('Verification email')->email()->disabled()->dehydrated(false),
                        TextInput::make('company')->maxLength(255),
                        TextInput::make('position')->maxLength(255),
                        Select::make('project_id')
                            ->label('Project')
                            ->options(fn (): array => Project::query()->ordered()->get()->mapWithKeys(
                                fn (Project $project): array => [$project->id => $project->localized('title', 'en')],
                            )->all())
                            ->searchable(),
                    ])
                    ->columns(2),
                ContentForms::localizedTabs(['content']),
                Section::make('Moderation')
                    ->schema([
                        Select::make('status')->options(TestimonialStatus::class)->required(),
                        TextInput::make('rating')->numeric()->minValue(1)->maxValue(5),
                        Toggle::make('is_featured')->label('Featured when approved'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('project'))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('company')->searchable(),
                TextColumn::make('project.title.en')->label('Project'),
                TextColumn::make('status')->badge()->searchable(),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('rating')->numeric()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(TestimonialStatus::class),
                SelectFilter::make('is_featured')
                    ->label('Featured')
                    ->options([1 => 'Featured', 0 => 'Not featured']),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Approved)
                    ->action(fn (Testimonial $record): Testimonial => app(TestimonialModerationService::class)->approve($record)),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Rejected)
                    ->action(fn (Testimonial $record): Testimonial => app(TestimonialModerationService::class)->reject($record)),
                Action::make('archive')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Archived)
                    ->action(fn (Testimonial $record): Testimonial => app(TestimonialModerationService::class)->archive($record)),
                Action::make('feature')
                    ->icon(Heroicon::OutlinedStar)
                    ->visible(fn (Testimonial $record): bool => ! $record->is_featured && $record->status === TestimonialStatus::Approved)
                    ->action(fn (Testimonial $record): Testimonial => app(TestimonialModerationService::class)->feature($record)),
                EditAction::make()
                    ->using(fn (Model $record, array $data): Model => app(TestimonialModerationService::class)->update($record, $data)),
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
            'index' => ManageTestimonials::route('/'),
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
