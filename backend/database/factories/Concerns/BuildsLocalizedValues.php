<?php

namespace Database\Factories\Concerns;

trait BuildsLocalizedValues
{
    /**
     * @return array{en: string, ar: string}
     */
    protected function localized(string $english, string $arabic = 'محتوى عربي تجريبي'): array
    {
        return [
            'en' => $english,
            'ar' => $arabic,
        ];
    }
}
