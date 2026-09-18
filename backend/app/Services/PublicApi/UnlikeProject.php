<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\ProjectInteractionResult;
use App\Data\PublicApi\VisitorContext;
use App\Exceptions\PublicApi\PublishedProjectNotFound;
use App\Models\ProjectLike;
use App\Queries\PublicApi\ProjectQuery;
use Illuminate\Support\Facades\DB;

final readonly class UnlikeProject
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

        return DB::transaction(function () use ($project, $visitor): ProjectInteractionResult {
            $deleted = ProjectLike::query()
                ->where('project_id', $project->id)
                ->where('visitor_id_hash', $visitor->visitorIdHash)
                ->delete() > 0;

            return new ProjectInteractionResult(
                count: ProjectLike::query()->where('project_id', $project->id)->count(),
                created: $deleted,
                liked: false,
            );
        });
    }
}
