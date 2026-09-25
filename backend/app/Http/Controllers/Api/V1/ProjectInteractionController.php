<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicApi\LocalizedRouteParameter;
use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectInteractionRequest;
use App\Http\Resources\Api\V1\ProjectInteractionResource;
use App\Services\PublicApi\GetProjectInteractionState;
use App\Services\PublicApi\LikeProject;
use App\Services\PublicApi\RegisterProjectView;
use App\Services\PublicApi\UnlikeProject;
use Illuminate\Http\JsonResponse;

class ProjectInteractionController extends Controller
{
    use ReturnsPublicApiResponses;

    public function state(ProjectInteractionRequest $request, string $slug, GetProjectInteractionState $service): JsonResponse
    {
        $parameter = LocalizedRouteParameter::fromRequest($slug, $request);
        $result = $service->handle($parameter->slug, $parameter->locale, $request->visitorContext());

        return $this->publicResource(new ProjectInteractionResource($result), $parameter->locale, cacheControl: 'no-store');
    }

    public function view(ProjectInteractionRequest $request, string $slug, RegisterProjectView $service): JsonResponse
    {
        $parameter = LocalizedRouteParameter::fromRequest($slug, $request);
        $result = $service->handle($parameter->slug, $parameter->locale, $request->visitorContext());

        return $this->publicResource(new ProjectInteractionResource($result), $parameter->locale, $result->created ? 201 : 200, 'no-store');
    }

    public function like(ProjectInteractionRequest $request, string $slug, LikeProject $service): JsonResponse
    {
        $parameter = LocalizedRouteParameter::fromRequest($slug, $request);
        $result = $service->handle($parameter->slug, $parameter->locale, $request->visitorContext());

        return $this->publicResource(new ProjectInteractionResource($result), $parameter->locale, $result->created ? 201 : 200, 'no-store');
    }

    public function unlike(ProjectInteractionRequest $request, string $slug, UnlikeProject $service): JsonResponse
    {
        $parameter = LocalizedRouteParameter::fromRequest($slug, $request);
        $result = $service->handle($parameter->slug, $parameter->locale, $request->visitorContext());

        return $this->publicResource(new ProjectInteractionResource($result), $parameter->locale, cacheControl: 'no-store');
    }
}
