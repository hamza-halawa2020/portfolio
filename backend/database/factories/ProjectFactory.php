<?php

namespace Database\Factories;

use App\Enums\PublicationStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectMedia;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'project_category_id' => ProjectCategory::factory(),
            'title' => $this->localized($title, 'مشروع تجريبي'),
            'slug' => $this->localized(Str::slug($title), 'mashroa-tajreebi'),
            'summary' => $this->localized(fake()->sentence(), 'ملخص عربي تجريبي'),
            'body' => $this->localized(fake()->paragraph(), 'تفاصيل عربية تجريبية'),
            'role' => $this->localized('Laravel developer', 'مطوّر Laravel'),
            'duration' => $this->localized('8 weeks', 'ثمانية أسابيع'),
            'industry' => $this->localized('Professional services', 'خدمات مهنية'),
            'challenge' => $this->localized(fake()->sentence(), 'تحدي عربي تجريبي'),
            'solution' => $this->localized(fake()->sentence(), 'حل عربي تجريبي'),
            'features' => $this->localized(fake()->sentence(), 'ميزات عربية تجريبية'),
            'development_challenges' => $this->localized(fake()->sentence(), 'تحديات تطوير تجريبية'),
            'results' => $this->localized(fake()->sentence(), 'نتائج عربية تجريبية'),
            'metrics' => $this->localized('Fictional performance metrics', 'مؤشرات تجريبية'),
            'cover_image_path' => 'projects/demo-cover.webp',
            'status' => PublicationStatus::Draft,
            'is_featured' => false,
            'sort_order' => fake()->numberBetween(0, 20),
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

    public function withMedia(int $count = 1): static
    {
        return $this->afterCreating(fn (Project $project): mixed => ProjectMedia::factory()
            ->count($count)
            ->for($project)
            ->create());
    }
}
