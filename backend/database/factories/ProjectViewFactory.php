<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectView;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ProjectView> */
class ProjectViewFactory extends Factory
{
    public function definition(): array
    {
        $viewedAt = fake()->dateTimeBetween('-30 days');

        return [
            'project_id' => Project::factory(),
            'visitor_id_hash' => hash('sha256', Str::uuid()->toString()),
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'user_agent_hash' => hash('sha256', fake()->userAgent()),
            'viewed_on' => $viewedAt->format('Y-m-d'),
            'viewed_at' => $viewedAt,
        ];
    }
}
