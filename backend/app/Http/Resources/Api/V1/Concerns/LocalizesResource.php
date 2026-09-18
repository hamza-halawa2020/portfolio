<?php

namespace App\Http\Resources\Api\V1\Concerns;

use App\Models\Concerns\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait LocalizesResource
{
    protected function locale(Request $request): string
    {
        $locale = $request->attributes->get('api_locale')
            ?? $request->query('locale')
            ?? $request->header('Accept-Language', 'en');

        $locale = strtolower(substr((string) $locale, 0, 2));

        return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
    }

    protected function localized(Model $model, string $attribute, Request $request): ?string
    {
        if (! in_array(HasLocalizedAttributes::class, class_uses_recursive($model), true)) {
            return null;
        }

        return $model->localized($attribute, $this->locale($request));
    }

    protected function publicMediaUrl(?string $path): ?string
    {
        return $path === null ? null : '/storage/'.$path;
    }
}
