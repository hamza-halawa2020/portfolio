<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\ShowRequest;

final readonly class LocalizedRouteParameter
{
    public function __construct(
        public string $slug,
        public string $locale,
    ) {}

    public static function fromRequest(string $slug, ShowRequest $request): self
    {
        return new self(slug: $slug, locale: $request->locale());
    }
}
