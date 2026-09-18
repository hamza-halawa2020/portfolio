<?php

namespace App\Services\PublicApi;

use App\Queries\PublicApi\PublicTaxonomyQuery;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListProjectCategories
{
    public function __construct(
        private PublicTaxonomyQuery $taxonomies,
    ) {}

    public function handle(): Collection
    {
        return $this->taxonomies->projectCategories();
    }
}
