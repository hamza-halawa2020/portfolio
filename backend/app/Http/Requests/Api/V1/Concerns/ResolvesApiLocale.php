<?php

namespace App\Http\Requests\Api\V1\Concerns;

trait ResolvesApiLocale
{
    public function locale(): string
    {
        $locale = $this->query('locale') ?: $this->header('Accept-Language', 'en');
        $locale = strtolower(substr((string) $locale, 0, 2));

        return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function localeRules(): array
    {
        return [
            'locale' => ['sometimes', 'string', 'in:en,ar'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareLocaleValidation(): array
    {
        $data = $this->all();

        if (! $this->has('locale') && $this->hasHeader('Accept-Language')) {
            $data['locale'] = strtolower(substr((string) $this->header('Accept-Language'), 0, 2));
        }

        return $data;
    }
}
