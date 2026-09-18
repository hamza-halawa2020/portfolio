<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\ServiceResource;
use App\Services\PublicApi\ListPublishedServices;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(IndexRequest $request, ListPublishedServices $service): JsonResponse
    {
        $services = $service->handle();

        return $this->publicResource(ServiceResource::collection($services), $request->locale());
    }
}
