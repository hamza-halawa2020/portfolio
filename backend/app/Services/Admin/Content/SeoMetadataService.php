<?php

namespace App\Services\Admin\Content;

use App\Models\SeoMetadata;
use Illuminate\Database\Eloquent\Model;

class SeoMetadataService
{
    public function __construct(
        private readonly ContentPayload $payload,
        private readonly MediaFileService $mediaFiles,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function saveFor(Model $record, array $data): void
    {
        $existing = $record->exists ? $record->seoMetadata : null;
        $data = $this->payload->mergeLocalized($existing ?? new SeoMetadata, $data, [
            'title',
            'description',
            'og_title',
            'og_description',
        ]);

        if ($this->isEmpty($data)) {
            return;
        }

        $oldImage = $existing?->og_image_path;

        /** @var SeoMetadata $seo */
        $seo = $record->seoMetadata()->updateOrCreate([], [
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_image_path' => $data['og_image_path'] ?? null,
            'robots_index' => (bool) ($data['robots_index'] ?? true),
            'robots_follow' => (bool) ($data['robots_follow'] ?? true),
            'structured_data' => $this->decodeJson($data['structured_data'] ?? null),
            'redirect_url' => $data['redirect_url'] ?? null,
        ]);

        if ($oldImage && $oldImage !== $seo->og_image_path) {
            $this->mediaFiles->deleteUnreferenced([$oldImage], $seo);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function isEmpty(array $data): bool
    {
        return collect($data)
            ->except(['robots_index', 'robots_follow'])
            ->filter(fn (mixed $value): bool => filled($value))
            ->isEmpty();
    }

    private function decodeJson(mixed $value): mixed
    {
        if (! is_string($value) || trim($value) === '') {
            return $value;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
}
