<?php

namespace App\Data\PublicApi;

final readonly class LocalizedRouteParameter
{
    public function __construct(
        public string $slug,
        public string $locale,
    ) {}

    public static function fromRequest(string $slug, object $request): self
    {
        return new self(slug: $slug, locale: $request->locale());
    }
}
