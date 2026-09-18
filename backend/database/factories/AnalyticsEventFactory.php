<?php

namespace Database\Factories;

use App\Enums\AnalyticsEventType;
use App\Models\AnalyticsEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<AnalyticsEvent> */
class AnalyticsEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', Str::uuid()->toString()),
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'user_agent_hash' => hash('sha256', fake()->userAgent()),
            'url' => fake()->url(),
            'referrer_domain' => fake()->optional()->domainName(),
            'device_category' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
            'browser' => fake()->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
            'country' => fake()->countryCode(),
            'occurred_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }
}
