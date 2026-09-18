<?php

namespace App\Services\Admin\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LocalizedSlugService
{
    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $slugs
     * @return array{en?: string, ar?: string}
     */
    public function normalizeAndValidate(string $modelClass, array $slugs, ?Model $ignore = null): array
    {
        $normalized = [];

        foreach (['en', 'ar'] as $locale) {
            $value = $this->normalize((string) Arr::get($slugs, $locale, ''));

            if ($value === '') {
                continue;
            }

            $duplicate = $modelClass::query()
                ->when($ignore?->exists, fn ($query) => $query->whereKeyNot($ignore->getKey()))
                ->where("slug->{$locale}", $value)
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    "slug.{$locale}" => "The {$locale} slug is already in use.",
                ]);
            }

            $normalized[$locale] = $value;
        }

        return $normalized;
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  callable(): TModel  $callback
     * @return TModel
     */
    public function transactionWithSlugLock(string $modelClass, callable $callback): Model
    {
        return DB::transaction(function () use ($modelClass, $callback): Model {
            if (DB::getDriverName() === 'mysql') {
                $model = new $modelClass;
                DB::table($model->getTable())->lockForUpdate()->select($model->getKeyName())->limit(1)->get();
            }

            return $callback();
        });
    }

    private function normalize(string $slug): string
    {
        $slug = trim($slug);

        if ($slug === '') {
            return '';
        }

        $slug = Str::lower($slug);
        $slug = preg_replace('/[\s_]+/u', '-', $slug) ?? $slug;
        $slug = preg_replace('/[^\pL\pN\-]+/u', '', $slug) ?? $slug;
        $slug = preg_replace('/-+/u', '-', $slug) ?? $slug;

        return trim($slug, '-');
    }
}
