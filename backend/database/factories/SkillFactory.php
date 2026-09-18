<?php

namespace Database\Factories;

use App\Models\Skill;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Skill> */
class SkillFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        return [
            'name' => $this->localized(fake()->unique()->word(), 'مهارة تجريبية'),
            'group' => fake()->randomElement(['backend', 'frontend', 'database', 'testing']),
            'level' => fake()->numberBetween(60, 95),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
