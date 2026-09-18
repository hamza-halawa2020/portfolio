<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\IndexRequest;

final readonly class PaginationOptions
{
    public function __construct(
        public int $perPage,
    ) {}

    public static function fromRequest(IndexRequest $request, int $default = 12): self
    {
        return new self(perPage: $request->perPage($default));
    }
}
