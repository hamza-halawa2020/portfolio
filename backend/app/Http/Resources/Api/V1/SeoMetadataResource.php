<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoMetadataResource extends JsonResource
{
    use LocalizesResource;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->localized($this->resource, 'title', $request),
            'description' => $this->localized($this->resource, 'description', $request),
            'canonical_url' => $this->canonical_url,
            'og_title' => $this->localized($this->resource, 'og_title', $request),
            'og_description' => $this->localized($this->resource, 'og_description', $request),
            'og_image_url' => $this->publicMediaUrl($this->og_image_path),
            'robots' => [
                'index' => (bool) $this->robots_index,
                'follow' => (bool) $this->robots_follow,
            ],
            'structured_data' => $this->structured_data,
            'redirect_url' => $this->redirect_url,
        ];
    }
}
