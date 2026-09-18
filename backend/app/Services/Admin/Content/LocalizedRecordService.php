<?php

namespace App\Services\Admin\Content;

use App\Models\BlogCategory;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class LocalizedRecordService
{
    public function __construct(
        private readonly ContentPayload $payload,
        private readonly LocalizedSlugService $slugs,
        private readonly SeoMetadataService $seo,
    ) {}

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $data
     */
    public function create(string $modelClass, array $data): Model
    {
        return $this->save(new $modelClass, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Model $record, array $data): Model
    {
        return $this->save($record, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(Model $record, array $data): Model
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = $record::class;

        return $this->slugs->transactionWithSlugLock($modelClass, function () use ($record, $data, $modelClass): Model {
            $seo = $this->payload->extractSeo($data);

            if (array_key_exists('slug', $data)) {
                $data['slug'] = $this->slugs->normalizeAndValidate($modelClass, (array) ($data['slug'] ?? []), $record);
            }

            $localized = match ($modelClass) {
                ProjectCategory::class, BlogCategory::class => ['name', 'slug', 'description'],
                Tag::class => ['name', 'slug'],
                Service::class => ['title', 'slug', 'description'],
                default => [],
            };

            $data = $this->payload->sanitizeLocalizedHtml($data, ['description']);
            $data = $this->payload->mergeLocalized($record, $data, $localized);
            $record->fill($data);
            $record->save();

            if (method_exists($record, 'seoMetadata')) {
                $this->seo->saveFor($record, $seo);
            }

            return $record;
        });
    }
}
