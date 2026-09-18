<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\SiteResource;
use App\Services\PublicApi\GetPublicSiteData;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(IndexRequest $request, GetPublicSiteData $service): JsonResponse
    {
        $site = $service->handle($request->locale());

        return $this->publicResource(new SiteResource($site), $site->locale);
    }
}
