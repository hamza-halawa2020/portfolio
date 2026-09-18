<?php

namespace App\Services\PublicApi;

use App\Exceptions\PublicApi\PublishedProjectNotFound;
use App\Models\Project;
use App\Queries\PublicApi\ProjectQuery;

final readonly class GetPublishedProject
{
    public function __construct(
        private ProjectQuery $projects,
    ) {}

    public function handle(string $slug, string $locale): Project
    {
        $project = $this->projects->findPublishedBySlug($slug, $locale);

        if (! $project) {
            throw PublishedProjectNotFound::forSlug($slug);
        }

        $project->setRelation('relatedProjects', $this->projects->relatedFor($project));

        return $project;
    }
}
