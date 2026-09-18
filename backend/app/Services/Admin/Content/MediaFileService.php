<?php

namespace App\Services\Admin\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaFileService
{
    /**
     * @param  array<int, string>  $paths
     */
    public function deleteUnreferenced(array $paths, ?Model $excluding = null): void
    {
        foreach (array_filter(array_unique($paths)) as $path) {
            if ($this->isReferenced($path, $excluding)) {
                continue;
            }

            Storage::disk($this->disk())->delete($path);
        }
    }

    public function disk(): string
    {
        return (string) config('portfolio.media.disk', 'public');
    }

    private function isReferenced(string $path, ?Model $excluding = null): bool
    {
        $queries = [
            \App\Models\Project::query()->where('cover_image_path', $path),
            \App\Models\BlogPost::query()->where('cover_image_path', $path),
            \App\Models\ProjectMedia::query()->where('path', $path)->orWhere('poster_path', $path),
            \App\Models\SeoMetadata::query()->where('og_image_path', $path),
        ];

        foreach ($queries as $query) {
            if ($excluding !== null) {
                $model = $query->getModel();

                if ($model::class === $excluding::class && $excluding->exists) {
                    $query->whereKeyNot($excluding->getKey());
                }
            }

            if ($query->exists()) {
                return true;
            }
        }

        return false;
    }
}
