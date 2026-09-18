<?php

namespace App\Filament\Resources\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class ContentForms
{
    /**
     * @param  array<int, string>  $fields
     */
    public static function localizedTabs(array $fields): Tabs
    {
        return Tabs::make('Bilingual content')
            ->tabs([
                Tab::make('English')
                    ->schema(static::localizedFields($fields, 'en')),
                Tab::make('Arabic')
                    ->schema(static::localizedFields($fields, 'ar')),
            ])
            ->columnSpanFull();
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<int, TextInput|Textarea|RichEditor>
     */
    public static function localizedFields(array $fields, string $locale): array
    {
        return array_map(fn (string $field): TextInput|Textarea|RichEditor => static::localizedField($field, $locale), $fields);
    }

    public static function seoSection(): Section
    {
        return Section::make('SEO')
            ->schema([
                static::localizedTabs([
                    'seo.title',
                    'seo.description',
                    'seo.og_title',
                    'seo.og_description',
                ]),
                TextInput::make('seo.canonical_url')->url()->maxLength(255),
                TextInput::make('seo.redirect_url')->url()->maxLength(255),
                static::imageUpload('seo.og_image_path', 'seo'),
                Toggle::make('seo.robots_index')->default(true),
                Toggle::make('seo.robots_follow')->default(true),
                Textarea::make('seo.structured_data')->json()->rows(6)->columnSpanFull(),
            ])
            ->collapsed()
            ->columnSpanFull();
    }

    public static function imageUpload(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->disk((string) config('portfolio.media.disk', 'public'))
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->acceptedFileTypes(config('portfolio.media.image_mimes', []))
            ->maxSize((int) config('portfolio.media.max_image_kb', 5120))
            ->previewable()
            ->downloadable()
            ->openable();
    }

    public static function videoUpload(string $name, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->disk((string) config('portfolio.media.disk', 'public'))
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(config('portfolio.media.video_mimes', []))
            ->maxSize((int) config('portfolio.media.max_video_kb', 51200))
            ->downloadable()
            ->openable();
    }

    public static function publicationSection(): Section
    {
        return Section::make('Publication')
            ->schema([
                Select::make('status')
                    ->options(\App\Enums\PublicationStatus::class)
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at'),
                Toggle::make('is_featured')->default(false),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
            ])
            ->columns(2);
    }

    public static function dateRangeSection(): Section
    {
        return Section::make('Dates')
            ->schema([
                DatePicker::make('starts_at')->required(),
                DatePicker::make('ends_at'),
                Toggle::make('is_current')->default(false),
                TextInput::make('sort_order')->numeric()->default(0)->required(),
            ])
            ->columns(2);
    }

    private static function localizedField(string $field, string $locale): TextInput|Textarea|RichEditor
    {
        $path = "{$field}.{$locale}";
        $label = str($field)->afterLast('.')->replace('_', ' ')->title()->append(" ({$locale})")->toString();

        return match (true) {
            str_contains($field, 'body'),
            str_contains($field, 'challenge'),
            str_contains($field, 'solution'),
            str_contains($field, 'features'),
            str_contains($field, 'development_challenges'),
            str_contains($field, 'results'),
            str_contains($field, 'metrics'),
            str_contains($field, 'description') => RichEditor::make($path)->label($label)->columnSpanFull(),
            str_contains($field, 'summary'),
            str_contains($field, 'excerpt'),
            str_contains($field, 'caption'),
            str_contains($field, 'alt_text') => Textarea::make($path)->label($label)->rows(3)->columnSpanFull(),
            default => TextInput::make($path)->label($label)->maxLength(255),
        };
    }
}
