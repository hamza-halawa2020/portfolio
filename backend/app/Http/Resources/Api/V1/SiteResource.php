<?php

namespace App\Http\Resources\Api\V1;

use App\Data\PublicApi\SiteData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var SiteData $site */
        $site = $this->resource;

        return [
            'settings' => $site->settings,
            'navigation' => $site->navigation,
            'services' => ServiceResource::collection($site->services),
            'skills' => SkillResource::collection($site->skills),
            'social_links' => SocialLinkResource::collection($site->socialLinks),
        ];
    }
}
