<?php

namespace App\Data\PublicApi;

final readonly class ProjectInteractionResult
{
    public function __construct(
        public int $count,
        public bool $created,
        public ?bool $liked = null,
    ) {}
}
