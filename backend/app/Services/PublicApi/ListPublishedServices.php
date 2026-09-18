<?php

namespace App\Services\PublicApi;

use App\Queries\PublicApi\PublicProfileQuery;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListPublishedServices
{
    public function __construct(
        private PublicProfileQuery $profile,
    ) {}

    public function handle(): Collection
    {
        return $this->profile->services();
    }
}
