<?php

namespace App\Data\PublicApi;

use Illuminate\Support\Collection;

final readonly class AboutData
{
    public function __construct(
        public string $locale,
        public Collection $experience,
        public Collection $skills,
        public Collection $technologies,
        public Collection $socialLinks,
    ) {}
}
