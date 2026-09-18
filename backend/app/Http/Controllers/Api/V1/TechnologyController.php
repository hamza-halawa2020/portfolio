<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\TechnologyResource;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;

class TechnologyController extends Controller
{
    use RespondsWithPublicApi;

    public function __invoke(IndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $technologies = Technology::query()
            ->visible()
            ->whereHas('projects', fn ($query) => $query->published())
            ->ordered()
            ->get();

        return $this->collection(TechnologyResource::collection($technologies), $locale);
    }
}
