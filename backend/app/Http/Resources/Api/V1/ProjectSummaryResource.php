<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectSummaryResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->localized($this->resource, 'slug', $request),
            'title' => $this->localized($this->resource, 'title', $request),
            'summary' => $this->localized($this->resource, 'summary', $request),
            'cover_image_url' => $this->publicMediaUrl($this->cover_image_path),
            'category' => new ProjectCategoryResource($this->whenLoaded('category')),
            'technologies' => TechnologyResource::collection($this->whenLoaded('technologies')),
            'is_featured' => (bool) $this->is_featured,
            'published_at' => $this->published_at?->toISOString(),
            'view_count' => (int) ($this->views_count ?? 0),
            'like_count' => (int) ($this->likes_count ?? 0),
        ];
    }
}
