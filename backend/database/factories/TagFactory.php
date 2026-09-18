<?php

namespace Database\Factories;

use App\Models\Tag;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Tag> */
class TagFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => $this->localized(Str::headline($name), 'وسم تجريبي'),
            'slug' => $this->localized(Str::slug($name), 'wasm-tajreebi'),
        ];
    }
}
