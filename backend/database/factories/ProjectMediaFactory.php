<?php

namespace Database\Factories;

use App\Enums\ProjectMediaType;
use App\Models\Project;
use App\Models\ProjectMedia;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProjectMedia> */
class ProjectMediaFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'type' => ProjectMediaType::Image,
            'path' => 'projects/demo-image.webp',
            'poster_path' => null,
            'caption' => $this->localized(fake()->sentence(), 'تعليق عربي تجريبي'),
            'alt_text' => $this->localized(fake()->sentence(4), 'وصف صورة تجريبي'),
            'mime_type' => 'image/webp',
            'file_size' => fake()->numberBetween(50_000, 500_000),
            'sort_order' => 0,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (): array => [
            'type' => ProjectMediaType::Video,
            'path' => 'projects/demo-video.mp4',
            'poster_path' => 'projects/demo-poster.webp',
            'mime_type' => 'video/mp4',
        ]);
    }
}
