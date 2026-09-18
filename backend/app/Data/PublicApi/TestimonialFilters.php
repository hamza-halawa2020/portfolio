<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\TestimonialIndexRequest;

final readonly class TestimonialFilters
{
    public function __construct(
        public string $locale,
        public PaginationOptions $pagination,
        public ?bool $featured,
        public ?string $projectSlug,
    ) {}

    public static function fromRequest(TestimonialIndexRequest $request): self
    {
        return new self(
            locale: $request->locale(),
            pagination: PaginationOptions::fromRequest($request),
            featured: $request->has('featured') ? $request->boolean('featured') : null,
            projectSlug: $request->filled('project') ? (string) $request->query('project') : null,
        );
    }
}
