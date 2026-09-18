<?php

namespace App\Http\Resources\Api\V1;

use App\Data\PublicApi\ProjectInteractionResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectInteractionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ProjectInteractionResult $result */
        $result = $this->resource;

        return array_filter([
            'count' => $result->count,
            'created' => $result->created,
            'liked' => $result->liked,
        ], fn (mixed $value): bool => $value !== null);
    }
}
