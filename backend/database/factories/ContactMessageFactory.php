<?php

namespace Database\Factories;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContactMessage> */
class ContactMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'company' => fake()->optional()->company(),
            'project_type' => fake()->randomElement(['website', 'api', 'dashboard']),
            'budget_range' => fake()->randomElement(['small', 'medium', 'enterprise']),
            'message' => fake()->paragraph(),
            'status' => ContactMessageStatus::New,
            'admin_notes' => null,
            'consented_at' => now(),
        ];
    }

    public function read(): static
    {
        return $this->state(fn (): array => ['status' => ContactMessageStatus::Read]);
    }
}
