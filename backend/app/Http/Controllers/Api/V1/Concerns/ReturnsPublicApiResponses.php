<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ReturnsPublicApiResponses
{
    protected int $publicReadTtl = 60;

    protected function publicResource(JsonResource $resource, string $locale, int $status = 200, ?string $cacheControl = null): JsonResponse
    {
        return $resource
            ->additional(['meta' => ['locale' => $locale]])
            ->response()
            ->setStatusCode($status)
            ->header('Cache-Control', $cacheControl ?? "public, max-age={$this->publicReadTtl}");
    }
}
