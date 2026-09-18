<?php

namespace App\Exceptions\PublicApi;

use RuntimeException;

final class PublishedProjectNotFound extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self("Published project [{$slug}] was not found.");
    }
}
