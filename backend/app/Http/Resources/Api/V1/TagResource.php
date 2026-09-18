<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->localized($this->resource, 'slug', $request),
            'name' => $this->localized($this->resource, 'name', $request),
        ];
    }
}
