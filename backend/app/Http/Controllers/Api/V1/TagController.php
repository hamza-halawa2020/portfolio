<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    use RespondsWithPublicApi;

    public function __invoke(IndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        return $this->collection(TagResource::collection(Tag::query()->whereHas('posts', fn ($query) => $query->published())->get()), $locale);
    }
}
