<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\TechnologyResource;
use App\Services\PublicApi\ListTechnologies;
use Illuminate\Http\JsonResponse;

class TechnologyController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(IndexRequest $request, ListTechnologies $service): JsonResponse
    {
        $technologies = $service->handle();

        return $this->publicResource(TechnologyResource::collection($technologies), $request->locale());
    }
}
