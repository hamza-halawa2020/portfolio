<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProjectCategory> */
class ProjectCategoryFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $this->localized(Str::headline($name), 'تصنيف تجريبي'),
            'slug' => $this->localized(Str::slug($name), 'tasneef-tajreebi'),
            'description' => $this->localized(fake()->sentence(), 'وصف عربي تجريبي'),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
