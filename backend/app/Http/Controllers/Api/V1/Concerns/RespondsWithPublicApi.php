<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

trait RespondsWithPublicApi
{
    protected int $publicReadTtl = 60;

    protected function locale(Request $request): string
    {
        $locale = $request->attributes->get('api_locale')
            ?? $request->query('locale')
            ?? $request->header('Accept-Language', 'en');
        $locale = strtolower(substr((string) $locale, 0, 2));

        return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
    }

    protected function prepareLocale(Request $request, string $locale): void
    {
        $request->attributes->set('api_locale', $locale);
    }

    protected function ok(JsonResource $resource, string $locale): JsonResponse
    {
        return $resource
            ->additional(['meta' => ['locale' => $locale]])
            ->response()
            ->header('Cache-Control', "public, max-age={$this->publicReadTtl}");
    }

    protected function collection(JsonResource $resource, string $locale): JsonResponse
    {
        return $resource
            ->additional(['meta' => ['locale' => $locale]])
            ->response()
            ->header('Cache-Control', "public, max-age={$this->publicReadTtl}");
    }

    protected function data(array $data, string $locale): JsonResponse
    {
        return response()
            ->json(['data' => $data, 'meta' => ['locale' => $locale]])
            ->header('Cache-Control', "public, max-age={$this->publicReadTtl}");
    }

    protected function localized(Model $model, string $attribute, string $locale): ?string
    {
        if (! in_array(HasLocalizedAttributes::class, class_uses_recursive($model), true)) {
            return null;
        }

        return $model->localized($attribute, $locale);
    }

    protected function matchesLocalizedSlug(Model $model, string $slug, string $locale): bool
    {
        if (! in_array(HasLocalizedAttributes::class, class_uses_recursive($model), true)) {
            return false;
        }

        return in_array($slug, array_filter([
            $model->localized('slug', $locale),
            $model->localized('slug', 'en'),
        ]), true);
    }

    /**
     * @template TKey of array-key
     * @template TModel of Model
     *
     * @param  EloquentCollection<TKey, TModel>|Collection<TKey, TModel>  $models
     */
    protected function findByLocalizedSlug(EloquentCollection|Collection $models, string $slug, string $locale): ?Model
    {
        return $models->first(fn (Model $model): bool => $this->matchesLocalizedSlug($model, $slug, $locale));
    }
}
