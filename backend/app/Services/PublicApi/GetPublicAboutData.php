<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\AboutData;
use App\Queries\PublicApi\PublicProfileQuery;

final readonly class GetPublicAboutData
{
    public function __construct(
        private PublicProfileQuery $profile,
    ) {}

    public function handle(string $locale): AboutData
    {
        return new AboutData(
            locale: $locale,
            experience: $this->profile->experience(),
            skills: $this->profile->skills(),
            technologies: $this->profile->technologies(),
            socialLinks: $this->profile->socialLinks(),
        );
    }
}
