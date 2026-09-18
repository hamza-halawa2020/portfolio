<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PublicApi\ContactSubmissionData;
use App\Http\Controllers\Api\V1\Concerns\ReturnsPublicApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactMessageRequest;
use App\Http\Resources\Api\V1\AcknowledgmentResource;
use App\Services\PublicApi\SubmitContactMessage;
use Illuminate\Http\JsonResponse;

class ContactSubmissionController extends Controller
{
    use ReturnsPublicApiResponses;

    public function __invoke(StoreContactMessageRequest $request, SubmitContactMessage $service): JsonResponse
    {
        $service->handle(ContactSubmissionData::fromRequest($request));

        return $this->publicResource(new AcknowledgmentResource(null), $request->locale(), 202, 'no-store');
    }
}
