<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevelopmentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_small_fictional_development_content_idempotently(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(1, Project::query()->where('slug->en', 'fictional-portfolio-platform')->count());
        $this->assertTrue(Service::query()->where('slug->en', 'laravel-api-development')->exists());
        $this->assertTrue(SiteSetting::query()->where('key', 'site.profile_headline')->exists());
    }
}
