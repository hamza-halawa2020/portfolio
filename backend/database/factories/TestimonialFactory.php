<?php

namespace Database\Factories;

use App\Enums\TestimonialStatus;
use App\Models\Project;
use App\Models\Testimonial;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Testimonial> */
class TestimonialFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->name(),
            'company' => fake()->company(),
            'position' => fake()->jobTitle(),
            'profile_image_path' => null,
            'content' => $this->localized(fake()->paragraph(), 'شهادة عربية تجريبية'),
            'rating' => fake()->numberBetween(4, 5),
            'contact_email' => fake()->safeEmail(),
            'status' => TestimonialStatus::Pending,
            'is_featured' => false,
            'consented_at' => now(),
            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => TestimonialStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }
}
