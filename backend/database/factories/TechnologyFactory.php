<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Technology> */
class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Laravel', 'Angular', 'MySQL', 'TypeScript', 'PHP', 'Tailwind', 'Playwright']).' '.fake()->unique()->numberBetween(1, 9999);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => null,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
