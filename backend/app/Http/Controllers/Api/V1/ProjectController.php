<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectIndexRequest;
use App\Http\Requests\Api\V1\ShowRequest;
use App\Http\Resources\Api\V1\ProjectDetailResource;
use App\Http\Resources\Api\V1\ProjectSummaryResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    use RespondsWithPublicApi;

    public function index(ProjectIndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $query = Project::query()
            ->published()
            ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with(['category', 'technologies'])
            ->withCount(['views', 'likes']);

        if ($request->filled('category')) {
            $category = $this->findByLocalizedSlug(ProjectCategory::query()->visible()->get(), (string) $request->query('category'), $locale);

            throw_if(! $category, ValidationException::withMessages(['category' => 'The selected category is invalid.']));
            $query->byCategory($category);
        }

        if ($request->filled('technology')) {
            $technology = Technology::query()->visible()->where('slug', $request->query('technology'))->first();

            throw_if(! $technology, ValidationException::withMessages(['technology' => 'The selected technology is invalid.']));
            $query->byTechnology($technology);
        }

        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(fn (Builder $query): Builder => $query
                ->where('title', 'like', $search)
                ->orWhere('summary', 'like', $search));
        }

        match ($request->query('sort', 'ordered')) {
            'latest' => $query->orderByDesc('published_at')->orderByDesc('id'),
            'oldest' => $query->orderBy('published_at')->orderBy('id'),
            'featured' => $query->orderByDesc('is_featured')->ordered(),
            default => $query->ordered(),
        };

        $projects = $query->paginate($request->perPage())->withQueryString();

        return ProjectSummaryResource::collection($projects)
            ->additional(['meta' => ['locale' => $locale]])
            ->response()
            ->header('Cache-Control', "public, max-age={$this->publicReadTtl}");
    }

    public function show(ShowRequest $request, string $slug): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $project = $this->findByLocalizedSlug(
            Project::query()
                ->published()
                ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
                ->with(['category', 'technologies', 'media' => fn ($query) => $query->ordered(), 'seoMetadata'])
                ->withCount(['views', 'likes'])
                ->get(),
            $slug,
            $locale,
        );

        abort_if(! $project, 404);

        $related = Project::query()
            ->published()
            ->whereKeyNot($project->id)
            ->where('project_category_id', $project->project_category_id)
            ->with(['category', 'technologies'])
            ->withCount(['views', 'likes'])
            ->ordered()
            ->limit(3)
            ->get();

        $project->setRelation('relatedProjects', $related);

        return $this->ok(new ProjectDetailResource($project), $locale);
    }
}
