<?php

namespace App\Services\Admin\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ContentPayload
{
    public function __construct(private readonly SanitizesRichText $sanitizer) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    public function mergeLocalized(Model $record, array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            $existing = (array) ($record->getAttribute($field) ?? []);
            $incoming = is_array($data[$field]) ? $data[$field] : [];
            $merged = $existing;

            foreach (['en', 'ar'] as $locale) {
                if (! array_key_exists($locale, $incoming)) {
                    continue;
                }

                $value = is_string($incoming[$locale]) ? trim($incoming[$locale]) : $incoming[$locale];

                if ($value === null || $value === '') {
                    continue;
                }

                $merged[$locale] = $value;
            }

            $data[$field] = $merged;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    public function sanitizeLocalizedHtml(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            foreach (['en', 'ar'] as $locale) {
                $path = "{$field}.{$locale}";

                if (! is_string(Arr::get($data, $path))) {
                    continue;
                }

                Arr::set($data, $path, $this->sanitizer->sanitize(Arr::get($data, $path)));
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function extractSeo(array &$data): array
    {
        $seo = is_array($data['seo'] ?? null) ? $data['seo'] : [];
        unset($data['seo']);

        return $seo;
    }
}
