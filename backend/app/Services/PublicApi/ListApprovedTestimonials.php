<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\TestimonialFilters;
use App\Queries\PublicApi\TestimonialQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListApprovedTestimonials
{
    public function __construct(
        private TestimonialQuery $testimonials,
    ) {}

    public function handle(TestimonialFilters $filters): LengthAwarePaginator
    {
        return $this->testimonials->paginateApproved($filters);
    }
}
