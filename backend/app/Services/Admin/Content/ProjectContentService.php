<?php

namespace App\Services\Admin\Content;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ProjectContentService
{
    public function __construct(
        private readonly ContentPayload $payload,
        private readonly LocalizedSlugService $slugs,
        private readonly SeoMetadataService $seo,
        private readonly MediaFileService $mediaFiles,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Project
    {
        return $this->save(new Project, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Project $project, array $data): Project
    {
        return $this->save($project, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(Project $project, array $data): Project
    {
        return $this->slugs->transactionWithSlugLock(Project::class, function () use ($project, $data): Model {
            $seo = $this->payload->extractSeo($data);
            $technologyIds = Arr::pull($data, 'technologies', []);
            $oldCover = $project->cover_image_path;

            $data['slug'] = $this->slugs->normalizeAndValidate(Project::class, (array) ($data['slug'] ?? []), $project);
            $data = $this->payload->sanitizeLocalizedHtml($data, [
                'body',
                'challenge',
                'solution',
                'features',
                'development_challenges',
                'results',
                'metrics',
            ]);
            $data = $this->payload->mergeLocalized($project, $data, [
                'title',
                'slug',
                'summary',
                'body',
                'role',
                'duration',
                'industry',
                'challenge',
                'solution',
                'features',
                'development_challenges',
                'results',
                'metrics',
            ]);

            $project->fill($data);
            $project->save();
            $project->technologies()->sync($technologyIds);
            $this->seo->saveFor($project, $seo);

            if ($oldCover && $oldCover !== $project->cover_image_path) {
                $this->mediaFiles->deleteUnreferenced([$oldCover], $project);
            }

            return $project;
        });
    }
}
