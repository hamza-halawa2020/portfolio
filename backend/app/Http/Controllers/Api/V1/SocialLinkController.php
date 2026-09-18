<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\SocialLinkResource;
use App\Services\PublicApi\ListPublicSocialLinks;
use Illuminate\Http\JsonResponse;

class SocialLinkController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(IndexRequest $request, ListPublicSocialLinks $service): JsonResponse
    {
        $links = $service->handle();

        return $this->publicResource(SocialLinkResource::collection($links), $request->locale());
    }
}
