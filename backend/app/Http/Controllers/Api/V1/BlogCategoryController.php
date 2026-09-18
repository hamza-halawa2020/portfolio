<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\BlogCategoryResource;
use App\Models\BlogCategory;
use Illuminate\Http\JsonResponse;

class BlogCategoryController extends Controller
{
    use RespondsWithPublicApi;

    public function __invoke(IndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        return $this->collection(
            BlogCategoryResource::collection(BlogCategory::query()->visible()->ordered()->get()),
            $locale,
        );
    }
}
