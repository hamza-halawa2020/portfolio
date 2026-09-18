<?php

namespace App\Queries\PublicApi;

use App\Data\PublicApi\ProjectFilters;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final readonly class ProjectQuery
{
    public function __construct(
        private LocalizedSlugMatcher $slugMatcher,
    ) {}

    public function paginatePublished(ProjectFilters $filters): LengthAwarePaginator
    {
        $query = $this->publishedBaseQuery()
            ->with(['category', 'technologies'])
            ->withCount(['views', 'likes']);

        $this->applyFilters($query, $filters);
        $this->applySort($query, $filters->sort);

        return $query->paginate($filters->pagination->perPage);
    }

    public function findPublishedBySlug(string $slug, string $locale): ?Project
    {
        /** @var Project|null $project */
        $project = $this->slugMatcher->first(
            $this->publishedBaseQuery()
                ->with(['category', 'technologies', 'media' => fn ($query) => $query->ordered(), 'seoMetadata'])
                ->withCount(['views', 'likes'])
                ->get(),
            $slug,
            $locale,
        );

        return $project;
    }

    public function relatedFor(Project $project, int $limit = 3): Collection
    {
        return $this->publishedBaseQuery()
            ->whereKeyNot($project->id)
            ->where('project_category_id', $project->project_category_id)
            ->with(['category', 'technologies'])
            ->withCount(['views', 'likes'])
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function findPublishedFilterProject(string $slug, string $locale): ?Project
    {
        /** @var Project|null $project */
        $project = $this->slugMatcher->first($this->publishedBaseQuery()->get(), $slug, $locale);

        return $project;
    }

    private function publishedBaseQuery(): Builder
    {
        return Project::query()
            ->published()
            ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    private function applyFilters(Builder $query, ProjectFilters $filters): void
    {
        if ($filters->categorySlug !== null) {
            $category = $this->slugMatcher->first(ProjectCategory::query()->visible()->get(), $filters->categorySlug, $filters->locale);
            throw_if(! $category, ValidationException::withMessages(['category' => 'The selected category is invalid.']));
            $query->byCategory($category);
        }

        if ($filters->technologySlug !== null) {
            $technology = Technology::query()->visible()->where('slug', $filters->technologySlug)->first();
            throw_if(! $technology, ValidationException::withMessages(['technology' => 'The selected technology is invalid.']));
            $query->byTechnology($technology);
        }

        if ($filters->featured !== null) {
            $query->where('is_featured', $filters->featured);
        }

        if ($filters->search !== null) {
            $search = '%'.$filters->search.'%';
            $query->where(fn (Builder $query): Builder => $query
                ->where('title', 'like', $search)
                ->orWhere('summary', 'like', $search));
        }
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'latest' => $query->orderByDesc('published_at')->orderByDesc('id'),
            'oldest' => $query->orderBy('published_at')->orderBy('id'),
            'featured' => $query->orderByDesc('is_featured')->ordered(),
            default => $query->ordered(),
        };
    }
}
