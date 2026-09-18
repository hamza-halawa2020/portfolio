<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectLike;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProjectLike> */
class ProjectLikeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'visitor_id_hash' => hash('sha256', Str::uuid()->toString()),
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'user_agent_hash' => hash('sha256', fake()->userAgent()),
            'liked_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}
