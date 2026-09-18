<?php

namespace App\Queries\PublicApi;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class LocalizedSlugMatcher
{
    public function matches(Model $model, string $slug, string $locale): bool
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
    public function first(EloquentCollection|Collection $models, string $slug, string $locale): ?Model
    {
        return $models->first(fn (Model $model): bool => $this->matches($model, $slug, $locale));
    }
}
