<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicApi\TestimonialFilters;
use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TestimonialIndexRequest;
use App\Http\Resources\Api\V1\TestimonialResource;
use App\Services\PublicApi\ListApprovedTestimonials;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(TestimonialIndexRequest $request, ListApprovedTestimonials $service): JsonResponse
    {
        $filters = TestimonialFilters::fromRequest($request);
        $testimonials = $service->handle($filters);

        return $this->publicResource(TestimonialResource::collection($testimonials), $filters->locale);
    }
}
