<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\BlogCategoryResource;
use App\Services\PublicApi\ListBlogCategories;
use Illuminate\Http\JsonResponse;

class BlogCategoryController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(IndexRequest $request, ListBlogCategories $service): JsonResponse
    {
        $categories = $service->handle();

        return $this->publicResource(BlogCategoryResource::collection($categories), $request->locale());
    }
}
