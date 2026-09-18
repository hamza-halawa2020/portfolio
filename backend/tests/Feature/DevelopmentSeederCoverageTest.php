<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactAttachment;
use App\Models\ContactMessage;
use App\Models\DailyAnalyticsSummary;
use App\Models\Experience;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectLike;
use App\Models\ProjectMedia;
use App\Models\ProjectView;
use App\Models\SeoMetadata;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\DevelopmentPortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DevelopmentSeederCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config()->set('portfolio.local_admin.name', 'Local Admin');
        config()->set('portfolio.local_admin.email', 'admin@example.com');
        config()->set('portfolio.local_admin.password', '12345678');
    }

    public function test_development_seeder_covers_business_tables_and_public_bilingual_content(): void
    {
        $this->seed(DevelopmentPortfolioSeeder::class);

        $this->assertGreaterThanOrEqual(2, User::query()->count());
        $this->assertGreaterThanOrEqual(3, ProjectCategory::query()->count());
        $this->assertGreaterThanOrEqual(8, Technology::query()->count());
        $this->assertGreaterThanOrEqual(6, Project::query()->count());
        $this->assertGreaterThanOrEqual(6, ProjectMedia::query()->count());
        $this->assertGreaterThan(0, ProjectView::query()->count());
        $this->assertGreaterThan(0, ProjectLike::query()->count());
        $this->assertGreaterThanOrEqual(5, Testimonial::query()->count());
        $this->assertGreaterThanOrEqual(2, BlogCategory::query()->count());
        $this->assertGreaterThanOrEqual(5, Tag::query()->count());
        $this->assertGreaterThanOrEqual(6, BlogPost::query()->count());
        $this->assertGreaterThanOrEqual(4, Service::query()->count());
        $this->assertGreaterThanOrEqual(3, Experience::query()->count());
        $this->assertGreaterThanOrEqual(8, Skill::query()->count());
        $this->assertGreaterThanOrEqual(4, ContactMessage::query()->count());
        $this->assertSame(0, ContactAttachment::query()->count());
        $this->assertGreaterThanOrEqual(4, SiteSetting::query()->count());
        $this->assertGreaterThanOrEqual(3, SocialLink::query()->count());
        $this->assertGreaterThanOrEqual(10, SeoMetadata::query()->count());
        $this->assertGreaterThan(0, AnalyticsEvent::query()->count());
        $this->assertGreaterThan(0, DailyAnalyticsSummary::query()->where('dimension', 'development_fixture')->count());

        $this->getJson('/api/v1/projects?locale=ar')->assertOk()->assertJsonPath('data.0.title', fn (string $title): bool => $title !== '');
        $this->getJson('/api/v1/posts?locale=en')->assertOk()->assertJsonCount(5, 'data');
    }

    public function test_development_seeder_is_repeatable_preserves_user_content_and_has_no_duplicate_pivots(): void
    {
        Project::factory()->published()->create(['slug' => ['en' => 'user-created-project']]);

        $this->seed(DevelopmentPortfolioSeeder::class);
        $firstCounts = $this->businessCounts();
        $this->seed(DevelopmentPortfolioSeeder::class);

        $this->assertSame($firstCounts, $this->businessCounts());
        $this->assertDatabaseHas('projects', ['slug->en' => 'user-created-project']);
        $this->assertSame(
            0,
            \DB::table('project_technology')
                ->select('project_id', 'technology_id')
                ->groupBy('project_id', 'technology_id')
                ->havingRaw('count(*) > 1')
                ->count(),
        );
        $this->assertSame(
            0,
            \DB::table('blog_post_tag')
                ->select('blog_post_id', 'tag_id')
                ->groupBy('blog_post_id', 'tag_id')
                ->havingRaw('count(*) > 1')
                ->count(),
        );
    }

    public function test_development_seeder_refuses_production_environment(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->expectException(RuntimeException::class);

        app(DevelopmentPortfolioSeeder::class)->run();
    }

    public function test_local_administrator_is_created_from_config_and_password_is_not_reset(): void
    {
        $this->seed(DevelopmentPortfolioSeeder::class);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('12345678', $admin->password));
        $this->assertNotSame('12345678', $admin->password);

        $firstHash = $admin->password;
        config()->set('portfolio.local_admin.password', 'changed-local-password');
        $this->seed(DevelopmentPortfolioSeeder::class);

        $this->assertSame($firstHash, $admin->refresh()->password);
        $this->actingAs(User::query()->where('email', 'fixture.viewer@example.test')->firstOrFail())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_local_administrator_does_not_elevate_existing_non_fixture_account(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'is_admin' => false]);

        $this->expectException(RuntimeException::class);

        $this->seed(DevelopmentPortfolioSeeder::class);
    }

    public function test_seeded_private_and_raw_identifier_values_do_not_leak_publicly(): void
    {
        $this->seed(DevelopmentPortfolioSeeder::class);

        $this->assertTrue(AnalyticsEvent::query()->where('visitor_id_hash', 'like', '%.%')->doesntExist());
        $this->assertTrue(ProjectView::query()->where('ip_hash', 'like', '%.%')->doesntExist());

        $this->getJson('/api/v1/testimonials')
            ->assertOk()
            ->assertJsonMissing(['contact_email' => 'dev-testimonial-a@example.test']);

        $this->getJson('/api/v1/site')
            ->assertOk()
            ->assertJsonMissing(['email' => 'dev-contact-new@example.test']);
    }

    /**
     * @return array<string, int>
     */
    private function businessCounts(): array
    {
        return [
            'projects' => Project::query()->count(),
            'project_technology' => \DB::table('project_technology')->count(),
            'blog_posts' => BlogPost::query()->count(),
            'blog_post_tag' => \DB::table('blog_post_tag')->count(),
            'project_views' => ProjectView::query()->count(),
            'project_likes' => ProjectLike::query()->count(),
            'analytics_events' => AnalyticsEvent::query()->count(),
            'daily_summaries' => DailyAnalyticsSummary::query()->count(),
        ];
    }
}
