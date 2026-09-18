<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->localized($this->resource, 'slug', $request),
            'title' => $this->localized($this->resource, 'title', $request),
            'description' => $this->localized($this->resource, 'description', $request),
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
        ];
    }
}
