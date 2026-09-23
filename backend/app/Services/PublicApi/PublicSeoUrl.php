<?php

namespace App\Services\PublicApi;

final readonly class PublicSeoUrl
{
    public function origin(): string
    {
        return rtrim((string) config('portfolio.seo.public_origin', 'https://example.com'), '/');
    }

    public function absolute(string $path): string
    {
        $normalized = str_starts_with($path, '/') ? $path : "/{$path}";

        return $this->origin().$normalized;
    }
}
