<?php

namespace Database\Factories;

use App\Models\SeoMetadata;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SeoMetadata> */
class SeoMetadataFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        return [
            'page_key' => fake()->unique()->slug(2),
            'title' => $this->localized(fake()->sentence(4), 'عنوان SEO تجريبي'),
            'description' => $this->localized(fake()->sentence(), 'وصف SEO تجريبي'),
            'canonical_url' => fake()->url(),
            'og_title' => $this->localized(fake()->sentence(4), 'عنوان مشاركة تجريبي'),
            'og_description' => $this->localized(fake()->sentence(), 'وصف مشاركة تجريبي'),
            'og_image_path' => null,
            'robots_index' => true,
            'robots_follow' => true,
            'structured_data' => ['@type' => 'WebPage'],
            'redirect_url' => null,
        ];
    }
}
