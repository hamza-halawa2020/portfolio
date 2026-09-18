<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\ProjectFilters;
use App\Queries\PublicApi\ProjectQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListPublishedProjects
{
    public function __construct(
        private ProjectQuery $projects,
    ) {}

    public function handle(ProjectFilters $filters): LengthAwarePaginator
    {
        return $this->projects->paginatePublished($filters);
    }
}
