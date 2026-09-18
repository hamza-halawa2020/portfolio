<?php

namespace Database\Factories;

use App\Models\DailyAnalyticsSummary;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DailyAnalyticsSummary> */
class DailyAnalyticsSummaryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-30 days'),
            'metric' => fake()->randomElement(['page_views', 'project_views', 'project_likes']),
            'dimension' => fake()->optional()->randomElement(['home', 'projects', 'contact']),
            'value' => fake()->numberBetween(1, 500),
        ];
    }
}
