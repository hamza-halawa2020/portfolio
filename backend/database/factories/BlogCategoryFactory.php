<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<BlogCategory> */
class BlogCategoryFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $this->localized(Str::headline($name), 'تصنيف مقالات تجريبي'),
            'slug' => $this->localized(Str::slug($name), 'maqalat-tajreebiya'),
            'description' => $this->localized(fake()->sentence(), 'وصف عربي تجريبي'),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
