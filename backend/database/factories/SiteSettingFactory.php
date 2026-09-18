<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Database\Factories\Concerns\BuildsLocalizedValues;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SiteSetting> */
class SiteSettingFactory extends Factory
{
    use BuildsLocalizedValues;

    public function definition(): array
    {
        return [
            'key' => 'setting.'.fake()->unique()->slug(2),
            'value' => $this->localized(fake()->sentence(), 'قيمة إعداد تجريبية'),
        ];
    }
}
