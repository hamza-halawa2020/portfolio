<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicApi\TestimonialSubmissionData;
use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTestimonialRequest;
use App\Http\Resources\Api\V1\AcknowledgmentResource;
use App\Services\PublicApi\SubmitTestimonial;
use Illuminate\Http\JsonResponse;

class TestimonialSubmissionController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(StoreTestimonialRequest $request, SubmitTestimonial $service): JsonResponse
    {
        $service->handle(TestimonialSubmissionData::fromRequest($request));

        return $this->publicResource(new AcknowledgmentResource(null), $request->locale(), 202, 'no-store');
    }
}
