<?php

namespace App\Data\PublicApi;

use Illuminate\Support\Carbon;

final readonly class SitemapUrl
{
    /**
     * @param  array<string, string>  $alternates
     */
    public function __construct(
        public string $path,
        public Carbon $lastModified,
        public array $alternates,
    ) {}
}
