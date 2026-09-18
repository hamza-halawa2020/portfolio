<?php

namespace App\Http\Resources\Api\V1;

use App\Data\PublicApi\AboutData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var AboutData $about */
        $about = $this->resource;

        return [
            'experience' => ExperienceResource::collection($about->experience),
            'skills' => SkillResource::collection($about->skills),
            'technologies' => TechnologyResource::collection($about->technologies),
            'social_links' => SocialLinkResource::collection($about->socialLinks),
        ];
    }
}
