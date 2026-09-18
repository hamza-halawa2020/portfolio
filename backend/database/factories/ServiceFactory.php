<?php

namespace Database\Factories;

use App\Models\Service;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => $this->localized(Str::headline($title), 'خدمة تجريبية'),
            'slug' => $this->localized(Str::slug($title), 'khidma-tajreebiya'),
            'description' => $this->localized(fake()->paragraph(), 'وصف خدمة عربي تجريبي'),
            'icon' => null,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
