<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\BlogPostIndexRequest;

final readonly class BlogPostFilters
{
    public function __construct(
        public string $locale,
        public PaginationOptions $pagination,
        public ?string $search,
        public ?string $categorySlug,
        public ?string $tagSlug,
        public ?bool $featured,
        public string $sort,
    ) {}

    public static function fromRequest(BlogPostIndexRequest $request): self
    {
        return new self(
            locale: $request->locale(),
            pagination: PaginationOptions::fromRequest($request),
            search: $request->filled('search') ? (string) $request->query('search') : null,
            categorySlug: $request->filled('category') ? (string) $request->query('category') : null,
            tagSlug: $request->filled('tag') ? (string) $request->query('tag') : null,
            featured: $request->has('featured') ? $request->boolean('featured') : null,
            sort: (string) $request->query('sort', 'latest'),
        );
    }
}
