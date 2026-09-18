<?php

namespace App\Models\Concerns;

trait HasLocalizedAttributes
{
    /**
     * @return array<string, string>
     */
    public function localizedValues(string $attribute): array
    {
        $value = $this->getAttribute($attribute);

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function localized(string $attribute, string $locale = 'en', string $fallback = 'en'): ?string
    {
        $values = $this->localizedValues($attribute);

        return $values[$locale] ?? $values[$fallback] ?? null;
    }
}
