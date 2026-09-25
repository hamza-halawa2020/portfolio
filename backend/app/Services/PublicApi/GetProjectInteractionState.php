<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\ProjectInteractionResult;
use App\Data\PublicApi\VisitorContext;
use App\Exceptions\PublicApi\PublishedProjectNotFound;
use App\Models\ProjectLike;
use App\Queries\PublicApi\ProjectQuery;

final readonly class GetProjectInteractionState
{
    public function __construct(
        private ProjectQuery $projects,
    ) {}

    public function handle(string $slug, string $locale, VisitorContext $visitor): ProjectInteractionResult
    {
        $project = $this->projects->findPublishedFilterProject($slug, $locale);

        if (! $project) {
            throw PublishedProjectNotFound::forSlug($slug);
        }

        $likes = ProjectLike::query()->where('project_id', $project->id);

        return new ProjectInteractionResult(
            count: (clone $likes)->count(),
            created: false,
            liked: (clone $likes)->where('visitor_id_hash', $visitor->visitorIdHash)->exists(),
        );
    }
}
