<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

class BlogPostDetailResource extends BlogPostSummaryResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'body' => $this->localized($this->resource, 'body', $request),
            'localized_slugs' => [
                'en' => $this->resource->localizedValues('slug')['en'] ?? null,
                'ar' => $this->resource->localizedValues('slug')['ar'] ?? null,
            ],
            'related_posts' => BlogPostSummaryResource::collection($this->whenLoaded('relatedPosts')),
            'seo' => new SeoMetadataResource($this->whenLoaded('seoMetadata')),
        ];
    }
}
