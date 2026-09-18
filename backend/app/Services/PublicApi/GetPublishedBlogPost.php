<?php

namespace App\Services\PublicApi;

use App\Exceptions\PublicApi\PublishedBlogPostNotFound;
use App\Models\BlogPost;
use App\Queries\PublicApi\BlogPostQuery;

final readonly class GetPublishedBlogPost
{
    public function __construct(
        private BlogPostQuery $posts,
    ) {}

    public function handle(string $slug, string $locale): BlogPost
    {
        $post = $this->posts->findPublishedBySlug($slug, $locale);

        if (! $post) {
            throw PublishedBlogPostNotFound::forSlug($slug);
        }

        $post->setRelation('relatedPosts', $this->posts->relatedFor($post));

        return $post;
    }
}
