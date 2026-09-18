<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Concerns\LocalizesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    use LocalizesResource;

    public function toArray(Request $request): array
    {
        return [
            'name' => $this->localized($this->resource, 'name', $request),
            'group' => $this->group,
            'level' => $this->level,
            'sort_order' => $this->sort_order,
        ];
    }
}
