<?php

namespace App\Queries\PublicApi;

use App\Models\BlogCategory;
use App\Models\ProjectCategory;
use App\Models\Tag;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class PublicTaxonomyQuery
{
    public function projectCategories(): Collection
    {
        return ProjectCategory::query()->visible()->ordered()->get();
    }

    public function technologiesUsedByPublishedProjects(): Collection
    {
        return Technology::query()
            ->visible()
            ->whereHas('projects', fn (Builder $query): Builder => $query->published())
            ->ordered()
            ->get();
    }

    public function blogCategories(): Collection
    {
        return BlogCategory::query()->visible()->ordered()->get();
    }

    public function tagsUsedByPublishedPosts(): Collection
    {
        return Tag::query()
            ->whereHas('posts', fn (Builder $query): Builder => $query->published())
            ->get();
    }
}
