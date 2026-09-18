<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\ProjectCategoryResource;
use App\Services\PublicApi\ListProjectCategories;
use Illuminate\Http\JsonResponse;

class ProjectCategoryController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(IndexRequest $request, ListProjectCategories $service): JsonResponse
    {
        $categories = $service->handle();

        return $this->publicResource(ProjectCategoryResource::collection($categories), $request->locale());
    }
}
