<?php

namespace Tests\Feature;

use App\Enums\PublicationStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicReadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_returns_published_localized_summary_with_aggregate_counts(): void
    {
        $category = ProjectCategory::factory()->create([
            'name' => ['en' => 'Web apps', 'ar' => 'تطبيقات ويب'],
            'slug' => ['en' => 'web-apps', 'ar' => 'tatbiqat-web'],
        ]);
        $technology = Technology::factory()->create(['slug' => 'laravel']);
        $published = Project::factory()->for($category, 'category')->published()->featured()->create([
            'title' => ['en' => 'English project', 'ar' => 'مشروع عربي'],
            'slug' => ['en' => 'english-project', 'ar' => 'mashroa-arabi'],
        ]);
        $published->technologies()->attach($technology);
        Project::factory()->create(['status' => PublicationStatus::Draft]);
        ProjectView::factory()->count(2)->for($published)->create();
        ProjectLike::factory()->for($published)->create();

        $response = $this->getJson('/api/v1/projects?locale=ar&category=tatbiqat-web&technology=laravel&featured=1');

        $response->assertOk()
            ->assertHeader('Cache-Control', 'max-age=60, public')
            ->assertJsonPath('meta.locale', 'ar')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'مشروع عربي')
            ->assertJsonPath('data.0.slug', 'mashroa-arabi')
            ->assertJsonPath('data.0.view_count', 2)
            ->assertJsonPath('data.0.like_count', 1)
            ->assertJsonMissing(['visitor_id_hash'])
            ->assertJsonMissing(['ip_hash']);
    }

    public function test_projects_index_validates_filters_sort_and_pagination(): void
    {
        $this->getJson('/api/v1/projects?sort=title')->assertUnprocessable()->assertJsonValidationErrors('sort');
        $this->getJson('/api/v1/projects?per_page=100')->assertUnprocessable()->assertJsonValidationErrors('per_page');
        $this->getJson('/api/v1/projects?search=x')->assertUnprocessable()->assertJsonValidationErrors('search');
        $this->getJson('/api/v1/projects?category=missing')->assertUnprocessable()->assertJsonValidationErrors('category');
        $this->getJson('/api/v1/projects?locale=fr')->assertUnprocessable()->assertJsonValidationErrors('locale');
    }

    public function test_project_detail_resolves_localized_slug_with_english_fallback_and_hides_private_data(): void
    {
        $project = Project::factory()->published()->create([
            'slug' => ['en' => 'fallback-project'],
            'title' => ['en' => 'Fallback project'],
            'summary' => ['en' => 'Fallback summary'],
            'body' => ['en' => 'Fallback body'],
        ]);
        ProjectMedia::factory()->for($project)->create(['sort_order' => 2, 'path' => 'projects/second.webp']);
        ProjectMedia::factory()->for($project)->create(['sort_order' => 1, 'path' => 'projects/first.webp']);
        SeoMetadata::factory()->for($project, 'seoable')->create([
            'title' => ['en' => 'SEO title'],
            'description' => ['en' => 'SEO description'],
        ]);

        $response = $this->getJson('/api/v1/projects/fallback-project?locale=ar');

        $response->assertOk()
            ->assertJsonPath('meta.locale', 'ar')
            ->assertJsonPath('data.title', 'Fallback project')
            ->assertJsonPath('data.body', 'Fallback body')
            ->assertJsonPath('data.media.0.url', '/storage/projects/first.webp')
            ->assertJsonPath('data.seo.title', 'SEO title')
            ->assertJsonMissing(['mime_type'])
            ->assertJsonMissing(['projects/first.webp', 'path']);
    }

    public function test_project_detail_returns_404_for_unknown_or_unpublished_slug(): void
    {
        Project::factory()->create(['slug' => ['en' => 'draft-project'], 'status' => PublicationStatus::Draft]);

        $this->getJson('/api/v1/projects/draft-project')->assertNotFound();
        $this->getJson('/api/v1/projects/missing-project')->assertNotFound();
    }

    public function test_blog_index_and_detail_return_only_published_posts(): void
    {
        $category = BlogCategory::factory()->create(['slug' => ['en' => 'notes', 'ar' => 'mulahazat']]);
        $tag = Tag::factory()->create(['slug' => ['en' => 'laravel', 'ar' => 'laravel']]);
        $post = BlogPost::factory()->for($category, 'category')->published()->featured()->create([
            'title' => ['en' => 'English post', 'ar' => 'مقال عربي'],
            'slug' => ['en' => 'english-post', 'ar' => 'maqal-arabi'],
        ]);
        $post->tags()->attach($tag);
        BlogPost::factory()->create(['status' => PublicationStatus::Draft]);
        SeoMetadata::factory()->for($post, 'seoable')->create(['title' => ['en' => 'Post SEO', 'ar' => 'SEO المقال']]);

        $this->getJson('/api/v1/posts?locale=ar&category=mulahazat&tag=laravel&featured=1')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'مقال عربي');

        $this->getJson('/api/v1/posts/maqal-arabi?locale=ar')
            ->assertOk()
            ->assertJsonPath('data.title', 'مقال عربي')
            ->assertJsonPath('data.seo.title', 'SEO المقال');
    }

    public function test_blog_detail_returns_404_for_future_or_draft_posts(): void
    {
        BlogPost::factory()->create(['slug' => ['en' => 'draft-post'], 'status' => PublicationStatus::Draft]);
        BlogPost::factory()->published()->create(['slug' => ['en' => 'future-post'], 'published_at' => now()->addDay()]);

        $this->getJson('/api/v1/posts/draft-post')->assertNotFound();
        $this->getJson('/api/v1/posts/future-post')->assertNotFound();
    }

    public function test_testimonials_endpoint_returns_only_approved_public_fields(): void
    {
        Testimonial::factory()->approved()->create(['content' => ['en' => 'Great work', 'ar' => 'عمل رائع']]);
        Testimonial::factory()->create(['content' => ['en' => 'Pending testimonial']]);

        $this->getJson('/api/v1/testimonials?locale=ar')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.content', 'عمل رائع')
            ->assertJsonMissing(['contact_email'])
            ->assertJsonMissing(['reviewed_at'])
            ->assertJsonMissing(['consented_at']);
    }

    public function test_site_endpoint_uses_public_setting_allowlist(): void
    {
        SiteSetting::factory()->create(['key' => 'site.profile_headline', 'value' => ['en' => 'Public headline']]);
        SiteSetting::factory()->create(['key' => 'internal.smtp_password', 'value' => ['en' => 'secret']]);
        Service::factory()->create();
        Skill::factory()->create();
        SocialLink::factory()->create(['label' => 'GitHub']);

        $response = $this->getJson('/api/v1/site');

        $response
            ->assertOk()
            ->assertJsonMissing(['internal.smtp_password'])
            ->assertJsonMissing(['secret']);

        $this->assertSame('Public headline', $response->json('data.settings')['site.profile_headline']['en']);
    }

    public function test_about_and_taxonomy_endpoints_are_available(): void
    {
        ProjectCategory::factory()->create();
        Technology::factory()->create();
        BlogCategory::factory()->create();
        Tag::factory()->create();
        Service::factory()->create();
        Skill::factory()->create();
        SocialLink::factory()->create();

        foreach (['/api/v1/about', '/api/v1/project-categories', '/api/v1/technologies', '/api/v1/blog-categories', '/api/v1/tags', '/api/v1/services', '/api/v1/social-links'] as $uri) {
            $this->getJson($uri)->assertOk()->assertJsonPath('meta.locale', 'en');
        }
    }

    public function test_project_collection_has_no_obvious_n_plus_one_regression(): void
    {
        $technology = Technology::factory()->create(['slug' => 'php']);
        Project::factory()->count(5)->published()->create()->each(fn (Project $project) => $project->technologies()->attach($technology));

        DB::enableQueryLog();
        $this->getJson('/api/v1/projects')->assertOk();

        $this->assertLessThanOrEqual(8, count(DB::getQueryLog()));
    }
}
