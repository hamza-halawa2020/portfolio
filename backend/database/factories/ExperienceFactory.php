<?php

namespace Database\Factories;

use App\Models\Experience;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Experience> */
class ExperienceFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-8 years', '-1 year');

        return [
            'title' => $this->localized(fake()->jobTitle(), 'دور وظيفي تجريبي'),
            'company' => $this->localized(fake()->company(), 'شركة تجريبية'),
            'location' => $this->localized(fake()->city(), 'مدينة تجريبية'),
            'description' => $this->localized(fake()->paragraph(), 'وصف خبرة عربي تجريبي'),
            'starts_at' => $startsAt,
            'ends_at' => fake()->dateTimeBetween($startsAt, 'now'),
            'is_current' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function current(): static
    {
        return $this->state(fn (): array => [
            'ends_at' => null,
            'is_current' => true,
        ]);
    }
}
