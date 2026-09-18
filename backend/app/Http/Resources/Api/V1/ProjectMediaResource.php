<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMediaResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type?->value ?? $this->type,
            'url' => $this->publicMediaUrl($this->path),
            'poster_url' => $this->publicMediaUrl($this->poster_path),
            'caption' => $this->localized($this->resource, 'caption', $request),
            'alt_text' => $this->localized($this->resource, 'alt_text', $request),
            'sort_order' => $this->sort_order,
        ];
    }
}
