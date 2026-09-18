<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'company' => $this->company,
            'position' => $this->position,
            'profile_image_url' => $this->publicMediaUrl($this->profile_image_path),
            'content' => $this->localized($this->resource, 'content', $request),
            'rating' => $this->rating,
            'is_featured' => (bool) $this->is_featured,
            'project' => new ProjectSummaryResource($this->whenLoaded('project')),
        ];
    }
}
