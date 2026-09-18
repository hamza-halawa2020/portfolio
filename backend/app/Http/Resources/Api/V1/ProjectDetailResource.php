<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

class ProjectDetailResource extends ProjectSummaryResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'body' => $this->localized($this->resource, 'body', $request),
            'role' => $this->localized($this->resource, 'role', $request),
            'duration' => $this->localized($this->resource, 'duration', $request),
            'industry' => $this->localized($this->resource, 'industry', $request),
            'challenge' => $this->localized($this->resource, 'challenge', $request),
            'solution' => $this->localized($this->resource, 'solution', $request),
            'features' => $this->localized($this->resource, 'features', $request),
            'development_challenges' => $this->localized($this->resource, 'development_challenges', $request),
            'results' => $this->localized($this->resource, 'results', $request),
            'metrics' => $this->localized($this->resource, 'metrics', $request),
            'media' => ProjectMediaResource::collection($this->whenLoaded('media')),
            'related_projects' => ProjectSummaryResource::collection($this->whenLoaded('relatedProjects')),
            'seo' => new SeoMetadataResource($this->whenLoaded('seoMetadata')),
        ];
    }
}
