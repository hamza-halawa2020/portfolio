<?php

namespace Database\Factories;

use App\Enums\PublicationStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogPost> */
class BlogPostFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'blog_category_id' => BlogCategory::factory(),
            'title' => $this->localized($title, 'مقال تجريبي'),
            'slug' => $this->localized(Str::slug($title), 'maqal-tajreebi'),
            'excerpt' => $this->localized(fake()->sentence(), 'مقتطف عربي تجريبي'),
            'body' => $this->localized(fake()->paragraphs(3, true), 'محتوى مقال عربي تجريبي'),
            'cover_image_path' => 'blog/demo-cover.webp',
            'status' => PublicationStatus::Draft,
            'is_featured' => false,
            'reading_time_minutes' => fake()->numberBetween(2, 8),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => PublicationStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }
}
