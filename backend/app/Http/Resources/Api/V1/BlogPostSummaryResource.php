<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostSummaryResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->localized($this->resource, 'slug', $request),
            'title' => $this->localized($this->resource, 'title', $request),
            'excerpt' => $this->localized($this->resource, 'excerpt', $request),
            'cover_image_url' => $this->publicMediaUrl($this->cover_image_path),
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'is_featured' => (bool) $this->is_featured,
            'reading_time_minutes' => $this->reading_time_minutes,
            'published_at' => $this->published_at?->toISOString(),
        ];
    }
}
