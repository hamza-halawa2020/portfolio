<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicApi\LocalizedRouteParameter;
use App\Data\PublicApi\ProjectFilters;
use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectIndexRequest;
use App\Http\Requests\Api\V1\ShowRequest;
use App\Http\Resources\Api\V1\ProjectDetailResource;
use App\Http\Resources\Api\V1\ProjectSummaryResource;
use App\Services\PublicApi\GetPublishedProject;
use App\Services\PublicApi\ListPublishedProjects;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    use ReturnsPublicApiResponses;

    public function index(ProjectIndexRequest $request, ListPublishedProjects $service): JsonResponse
    {
        $filters = ProjectFilters::fromRequest($request);
        $projects = $service->handle($filters);

        return $this->publicResource(ProjectSummaryResource::collection($projects), $filters->locale);
    }

    public function show(ShowRequest $request, string $slug, GetPublishedProject $service): JsonResponse
    {
        $parameter = LocalizedRouteParameter::fromRequest($slug, $request);
        $project = $service->handle($parameter->slug, $parameter->locale);

        return $this->publicResource(new ProjectDetailResource($project), $parameter->locale);
    }
}
