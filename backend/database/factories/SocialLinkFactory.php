<?php

namespace Database\Factories;

use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SocialLink> */
class SocialLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => fake()->randomElement(['GitHub', 'LinkedIn', 'Portfolio']),
            'url' => fake()->url(),
            'icon' => null,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
