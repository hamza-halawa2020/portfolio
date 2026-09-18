<?php

namespace App\Exceptions\PublicApi;

use RuntimeException;

final class PublishedBlogPostNotFound extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self("Published blog post [{$slug}] was not found.");
    }
}
