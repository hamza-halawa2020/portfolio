<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\ProjectInteractionResult;
use App\Data\PublicApi\VisitorContext;
use App\Exceptions\PublicApi\PublishedProjectNotFound;
use App\Models\ProjectLike;
use App\Queries\PublicApi\ProjectQuery;
use Illuminate\Support\Facades\DB;

final readonly class LikeProject
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
            $now = now();
            $created = ProjectLike::query()->insertOrIgnore([
                'project_id' => $project->id,
                'visitor_id_hash' => $visitor->visitorIdHash,
                'ip_hash' => $visitor->ipHash,
                'user_agent_hash' => $visitor->userAgentHash,
                'liked_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]) === 1;

            return new ProjectInteractionResult(
                count: ProjectLike::query()->where('project_id', $project->id)->count(),
                created: $created,
                liked: true,
            );
        });
    }
}
