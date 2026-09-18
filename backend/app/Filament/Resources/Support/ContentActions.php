<?php

namespace App\Filament\Resources\Support;

use App\Models\BlogPost;
use App\Models\Project;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

class ContentActions
{
    public static function create(string $serviceClass, string $method = 'create'): CreateAction
    {
        return CreateAction::make()
            ->using(fn (array $data): Model => app($serviceClass)->{$method}($data));
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function createForModel(string $serviceClass, string $modelClass, string $method = 'create'): CreateAction
    {
        return CreateAction::make()
            ->using(fn (array $data): Model => app($serviceClass)->{$method}($modelClass, $data));
    }

    public static function edit(string $serviceClass, string $method = 'update'): EditAction
    {
        return EditAction::make()
            ->mutateRecordDataUsing(fn (array $data, Model $record): array => static::withFormRelations($data, $record))
            ->using(fn (Model $record, array $data): Model => app($serviceClass)->{$method}($record, $data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withFormRelations(array $data, Model $record): array
    {
        if (method_exists($record, 'seoMetadata')) {
            $data['seo'] = $record->seoMetadata?->attributesToArray() ?? [
                'robots_index' => true,
                'robots_follow' => true,
            ];

            if (is_array($data['seo']['structured_data'] ?? null)) {
                $data['seo']['structured_data'] = json_encode($data['seo']['structured_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        }

        if ($record instanceof Project) {
            $data['technologies'] = $record->technologies()->pluck('technologies.id')->all();
        }

        if ($record instanceof BlogPost) {
            $data['tags'] = $record->tags()->pluck('tags.id')->all();
        }

        return $data;
    }
}
