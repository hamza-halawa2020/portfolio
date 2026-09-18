<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\BlogPostFilters;
use App\Queries\PublicApi\BlogPostQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListPublishedBlogPosts
{
    public function __construct(
        private BlogPostQuery $posts,
    ) {}

    public function handle(BlogPostFilters $filters): LengthAwarePaginator
    {
        return $this->posts->paginatePublished($filters);
    }
}
