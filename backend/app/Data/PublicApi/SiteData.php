<?php

namespace App\Data\PublicApi;

use Illuminate\Support\Collection;

final readonly class SiteData
{
    public function __construct(
        public string $locale,
        public array $settings,
        public array $navigation,
        public Collection $services,
        public Collection $skills,
        public Collection $socialLinks,
    ) {}
}
