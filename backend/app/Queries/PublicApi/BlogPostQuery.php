<?php

namespace App\Queries\PublicApi;

use App\Data\PublicApi\BlogPostFilters;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final readonly class BlogPostQuery
{
    public function __construct(
        private LocalizedSlugMatcher $slugMatcher,
    ) {}

    public function paginatePublished(BlogPostFilters $filters): LengthAwarePaginator
    {
        $query = $this->publishedBaseQuery()->with(['category', 'tags']);

        $this->applyFilters($query, $filters);
        $this->applySort($query, $filters->sort);

        return $query->paginate($filters->pagination->perPage);
    }

    public function findPublishedBySlug(string $slug, string $locale): ?BlogPost
    {
        /** @var BlogPost|null $post */
        $post = $this->slugMatcher->first(
            $this->publishedBaseQuery()
                ->with(['category', 'tags', 'seoMetadata'])
                ->get(),
            $slug,
            $locale,
        );

        return $post;
    }

    public function relatedFor(BlogPost $post, int $limit = 3): Collection
    {
        return $this->publishedBaseQuery()
            ->whereKeyNot($post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->with(['category', 'tags'])
            ->recent()
            ->limit($limit)
            ->get();
    }

    private function publishedBaseQuery(): Builder
    {
        return BlogPost::query()
            ->published()
            ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    private function applyFilters(Builder $query, BlogPostFilters $filters): void
    {
        if ($filters->categorySlug !== null) {
            $category = $this->slugMatcher->first(BlogCategory::query()->visible()->get(), $filters->categorySlug, $filters->locale);
            throw_if(! $category, ValidationException::withMessages(['category' => 'The selected category is invalid.']));
            $query->byCategory($category);
        }

        if ($filters->tagSlug !== null) {
            $tag = $this->slugMatcher->first(Tag::query()->get(), $filters->tagSlug, $filters->locale);
            throw_if(! $tag, ValidationException::withMessages(['tag' => 'The selected tag is invalid.']));
            $query->whereHas('tags', fn (Builder $query): Builder => $query->whereKey($tag->id));
        }

        if ($filters->featured !== null) {
            $query->where('is_featured', $filters->featured);
        }

        if ($filters->search !== null) {
            $search = '%'.$filters->search.'%';
            $query->where(fn (Builder $query): Builder => $query
                ->where('title', 'like', $search)
                ->orWhere('excerpt', 'like', $search));
        }
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('published_at')->orderBy('id'),
            'featured' => $query->orderByDesc('is_featured')->recent(),
            default => $query->recent(),
        };
    }
}
