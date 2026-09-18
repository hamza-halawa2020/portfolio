<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'title' => $this->localized($this->resource, 'title', $request),
            'company' => $this->localized($this->resource, 'company', $request),
            'location' => $this->localized($this->resource, 'location', $request),
            'description' => $this->localized($this->resource, 'description', $request),
            'starts_at' => $this->starts_at?->toDateString(),
            'ends_at' => $this->ends_at?->toDateString(),
            'is_current' => (bool) $this->is_current,
            'sort_order' => $this->sort_order,
        ];
    }
}
