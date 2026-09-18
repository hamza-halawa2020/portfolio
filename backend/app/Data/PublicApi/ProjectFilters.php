<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\ProjectIndexRequest;

final readonly class ProjectFilters
{
    public function __construct(
        public string $locale,
        public PaginationOptions $pagination,
        public ?string $search,
        public ?string $categorySlug,
        public ?string $technologySlug,
        public ?bool $featured,
        public string $sort,
    ) {}

    public static function fromRequest(ProjectIndexRequest $request): self
    {
        return new self(
            locale: $request->locale(),
            pagination: PaginationOptions::fromRequest($request),
            search: $request->filled('search') ? (string) $request->query('search') : null,
            categorySlug: $request->filled('category') ? (string) $request->query('category') : null,
            technologySlug: $request->filled('technology') ? (string) $request->query('technology') : null,
            featured: $request->has('featured') ? $request->boolean('featured') : null,
            sort: (string) $request->query('sort', 'ordered'),
        );
    }
}
