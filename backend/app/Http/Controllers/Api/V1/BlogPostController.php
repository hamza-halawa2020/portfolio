<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicApi\BlogPostFilters;
use App\Data\PublicApi\LocalizedRouteParameter;
use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BlogPostIndexRequest;
use App\Http\Requests\Api\V1\ShowRequest;
use App\Http\Resources\Api\V1\BlogPostDetailResource;
use App\Http\Resources\Api\V1\BlogPostSummaryResource;
use App\Services\PublicApi\GetPublishedBlogPost;
use App\Services\PublicApi\ListPublishedBlogPosts;
use Illuminate\Http\JsonResponse;

class BlogPostController extends Controller
{
    use ReturnsPublicApiResponses;

    public function index(BlogPostIndexRequest $request, ListPublishedBlogPosts $service): JsonResponse
    {
        $filters = BlogPostFilters::fromRequest($request);
        $posts = $service->handle($filters);

        return $this->publicResource(BlogPostSummaryResource::collection($posts), $filters->locale);
    }

    public function show(ShowRequest $request, string $slug, GetPublishedBlogPost $service): JsonResponse
    {
        $parameter = LocalizedRouteParameter::fromRequest($slug, $request);
        $post = $service->handle($parameter->slug, $parameter->locale);

        return $this->publicResource(new BlogPostDetailResource($post), $parameter->locale);
    }
}
