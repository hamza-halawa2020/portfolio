<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BlogPostIndexRequest;
use App\Http\Requests\Api\V1\ShowRequest;
use App\Http\Resources\Api\V1\BlogPostDetailResource;
use App\Http\Resources\Api\V1\BlogPostSummaryResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BlogPostController extends Controller
{
    use RespondsWithPublicApi;

    public function index(BlogPostIndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $query = BlogPost::query()
            ->published()
            ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with(['category', 'tags']);

        if ($request->filled('category')) {
            $category = $this->findByLocalizedSlug(BlogCategory::query()->visible()->get(), (string) $request->query('category'), $locale);
            throw_if(! $category, ValidationException::withMessages(['category' => 'The selected category is invalid.']));
            $query->byCategory($category);
        }

        if ($request->filled('tag')) {
            $tag = $this->findByLocalizedSlug(Tag::query()->get(), (string) $request->query('tag'), $locale);
            throw_if(! $tag, ValidationException::withMessages(['tag' => 'The selected tag is invalid.']));
            $query->whereHas('tags', fn (Builder $query): Builder => $query->whereKey($tag->id));
        }

        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(fn (Builder $query): Builder => $query
                ->where('title', 'like', $search)
                ->orWhere('excerpt', 'like', $search));
        }

        match ($request->query('sort', 'latest')) {
            'oldest' => $query->orderBy('published_at')->orderBy('id'),
            'featured' => $query->orderByDesc('is_featured')->recent(),
            default => $query->recent(),
        };

        $posts = $query->paginate($request->perPage())->withQueryString();

        return BlogPostSummaryResource::collection($posts)
            ->additional(['meta' => ['locale' => $locale]])
            ->response()
            ->header('Cache-Control', "public, max-age={$this->publicReadTtl}");
    }

    public function show(ShowRequest $request, string $slug): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $post = $this->findByLocalizedSlug(
            BlogPost::query()
                ->published()
                ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
                ->with(['category', 'tags', 'seoMetadata'])
                ->get(),
            $slug,
            $locale,
        );

        abort_if(! $post, 404);

        $related = BlogPost::query()
            ->published()
            ->whereKeyNot($post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->with(['category', 'tags'])
            ->recent()
            ->limit(3)
            ->get();

        $post->setRelation('relatedPosts', $related);

        return $this->ok(new BlogPostDetailResource($post), $locale);
    }
}
