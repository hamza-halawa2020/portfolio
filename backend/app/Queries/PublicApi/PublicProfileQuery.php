<?php

namespace App\Queries\PublicApi;

use App\Models\Experience;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Collection;

final class PublicProfileQuery
{
    public function experience(): Collection
    {
        return Experience::query()->ordered()->get();
    }

    public function skills(?int $limit = null): Collection
    {
        $query = Skill::query()->visible()->ordered();

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function technologies(): Collection
    {
        return Technology::query()->visible()->ordered()->get();
    }

    public function services(?int $limit = null): Collection
    {
        $query = Service::query()->visible()->ordered();

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function socialLinks(): Collection
    {
        return SocialLink::query()->visible()->ordered()->get();
    }
}
