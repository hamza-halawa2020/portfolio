<?php

namespace Tests\Feature;

use App\Enums\PublicationStatus;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\SeoMetadata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_payloads_include_localized_slug_mappings(): void
    {
        Project::factory()->published()->create([
            'slug' => ['en' => 'english-project', 'ar' => 'mashroa-arabi'],
        ]);
        BlogPost::factory()->published()->create([
            'slug' => ['en' => 'english-post', 'ar' => 'maqal-arabi'],
        ]);

        $this->getJson('/api/v1/projects/english-project?locale=en')
            ->assertOk()
            ->assertJsonPath('data.localized_slugs.en', 'english-project')
            ->assertJsonPath('data.localized_slugs.ar', 'mashroa-arabi');

        $this->getJson('/api/v1/posts/maqal-arabi?locale=ar')
            ->assertOk()
            ->assertJsonPath('data.localized_slugs.en', 'english-post')
            ->assertJsonPath('data.localized_slugs.ar', 'maqal-arabi');
    }

    public function test_sitemap_contains_static_and_published_localized_content_only(): void
    {
        config()->set('portfolio.seo.public_origin', 'https://portfolio.example');

        $project = Project::factory()->published()->create([
            'slug' => ['en' => 'public-project', 'ar' => 'mashroa-aam'],
            'updated_at' => now()->subDay(),
        ]);
        $noindexProject = Project::factory()->published()->create(['slug' => ['en' => 'hidden-project', 'ar' => 'hidden-ar']]);
        SeoMetadata::factory()->for($noindexProject, 'seoable')->create(['robots_index' => false]);
        Project::factory()->create(['slug' => ['en' => 'draft-project'], 'status' => PublicationStatus::Draft]);

        BlogPost::factory()->published()->create(['slug' => ['en' => 'public-post', 'ar' => 'maqal-aam']]);
        BlogPost::factory()->published()->create(['slug' => ['en' => 'future-post'], 'published_at' => now()->addDay()]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = $response->getContent();
        $this->assertStringContainsString('https://portfolio.example/en', $xml);
        $this->assertStringContainsString('https://portfolio.example/en/projects/public-project', $xml);
        $this->assertStringContainsString('https://portfolio.example/ar/projects/mashroa-aam', $xml);
        $this->assertStringContainsString('hreflang="x-default"', $xml);
        $this->assertStringContainsString($project->updated_at->toDateString(), $xml);
        $this->assertStringContainsString('https://portfolio.example/en/blog/public-post', $xml);
        $this->assertStringNotContainsString('hidden-project', $xml);
        $this->assertStringNotContainsString('draft-project', $xml);
        $this->assertStringNotContainsString('future-post', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
        $this->assertStringNotContainsString('/api/', $xml);
    }

    public function test_robots_blocks_non_production_and_references_sitemap_in_indexable_production(): void
    {
        config()->set('portfolio.seo.public_origin', 'https://portfolio.example');
        config()->set('portfolio.seo.indexing_enabled', false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee("User-agent: *\nDisallow: /", false)
            ->assertDontSee('Sitemap:', false);

        app()->detectEnvironment(fn (): string => 'production');
        config()->set('portfolio.seo.indexing_enabled', true);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Allow: /', false)
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Disallow: /api', false)
            ->assertSee('Sitemap: https://portfolio.example/sitemap.xml', false);
    }
}
