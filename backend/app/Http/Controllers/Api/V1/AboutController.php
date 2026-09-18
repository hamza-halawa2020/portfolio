<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\ExperienceResource;
use App\Http\Resources\Api\V1\SkillResource;
use App\Http\Resources\Api\V1\SocialLinkResource;
use App\Http\Resources\Api\V1\TechnologyResource;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    use RespondsWithPublicApi;

    public function __invoke(IndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        return $this->data([
            'experience' => ExperienceResource::collection(Experience::query()->ordered()->get())->resolve($request),
            'skills' => SkillResource::collection(Skill::query()->visible()->ordered()->get())->resolve($request),
            'technologies' => TechnologyResource::collection(Technology::query()->visible()->ordered()->get())->resolve($request),
            'social_links' => SocialLinkResource::collection(SocialLink::query()->visible()->ordered()->get())->resolve($request),
        ], $locale);
    }
}
