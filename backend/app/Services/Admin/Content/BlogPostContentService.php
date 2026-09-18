<?php

namespace App\Services\Admin\Content;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BlogPostContentService
{
    public function __construct(
        private readonly ContentPayload $payload,
        private readonly LocalizedSlugService $slugs,
        private readonly SeoMetadataService $seo,
        private readonly MediaFileService $mediaFiles,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): BlogPost
    {
        return $this->save(new BlogPost, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(BlogPost $post, array $data): BlogPost
    {
        return $this->save($post, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(BlogPost $post, array $data): BlogPost
    {
        return $this->slugs->transactionWithSlugLock(BlogPost::class, function () use ($post, $data): Model {
            $seo = $this->payload->extractSeo($data);
            $tagIds = Arr::pull($data, 'tags', []);
            $oldCover = $post->cover_image_path;

            $data['slug'] = $this->slugs->normalizeAndValidate(BlogPost::class, (array) ($data['slug'] ?? []), $post);
            $data = $this->payload->sanitizeLocalizedHtml($data, ['body']);
            $data = $this->payload->mergeLocalized($post, $data, ['title', 'slug', 'excerpt', 'body']);
            $data['reading_time_minutes'] = $this->readingTime($data['body'] ?? $post->body ?? []);

            $post->fill($data);
            $post->save();
            $post->tags()->sync($tagIds);
            $this->seo->saveFor($post, $seo);

            if ($oldCover && $oldCover !== $post->cover_image_path) {
                $this->mediaFiles->deleteUnreferenced([$oldCover], $post);
            }

            return $post;
        });
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function readingTime(array $body): int
    {
        $words = str_word_count(strip_tags(implode(' ', array_filter($body, 'is_string'))));

        return max(1, (int) ceil($words / 200));
    }
}
