<?php

namespace App\Services\Admin\Content;

use App\Enums\ProjectMediaType;
use App\Models\ProjectMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProjectMediaContentService
{
    public function __construct(
        private readonly ContentPayload $payload,
        private readonly MediaFileService $mediaFiles,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ProjectMedia
    {
        return $this->save(new ProjectMedia, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProjectMedia $media, array $data): ProjectMedia
    {
        return $this->save($media, $data);
    }

    public function delete(ProjectMedia $media): void
    {
        $paths = array_filter([$media->path, $media->poster_path]);

        $media->delete();

        $this->mediaFiles->deleteUnreferenced($paths, $media);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(ProjectMedia $media, array $data): ProjectMedia
    {
        $oldPaths = array_filter([$media->path, $media->poster_path]);
        $data = $this->payload->mergeLocalized($media, $data, ['caption', 'alt_text']);
        $data['type'] = $this->resolveType((string) ($data['path'] ?? $media->path));
        $data['mime_type'] = $this->mimeType((string) ($data['path'] ?? $media->path));
        $data['file_size'] = $this->fileSize((string) ($data['path'] ?? $media->path));

        if (! empty($data['poster_path'])) {
            $this->assertAllowedMime((string) $data['poster_path'], 'image');
        }

        $media->fill($data);
        $media->save();

        $this->mediaFiles->deleteUnreferenced(array_diff($oldPaths, [$media->path, $media->poster_path]), $media);

        return $media;
    }

    private function resolveType(string $path): ProjectMediaType
    {
        $mime = $this->mimeType($path);

        if (in_array($mime, config('portfolio.media.image_mimes', []), true)) {
            return ProjectMediaType::Image;
        }

        if (in_array($mime, config('portfolio.media.video_mimes', []), true)) {
            return ProjectMediaType::Video;
        }

        throw ValidationException::withMessages([
            'path' => 'The media file must be an allowed image, MP4, or WebM file.',
        ]);
    }

    private function assertAllowedMime(string $path, string $kind): void
    {
        $mime = $this->mimeType($path);
        $allowed = config("portfolio.media.{$kind}_mimes", []);

        if (! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages([
                'poster_path' => 'The poster image must be an allowed image file.',
            ]);
        }
    }

    private function mimeType(string $path): string
    {
        $mime = Storage::disk($this->mediaFiles->disk())->mimeType($path);

        if (! is_string($mime) || $mime === '') {
            throw ValidationException::withMessages([
                'path' => 'The uploaded media file could not be verified.',
            ]);
        }

        return $mime;
    }

    private function fileSize(string $path): int
    {
        return (int) Storage::disk($this->mediaFiles->disk())->size($path);
    }
}
