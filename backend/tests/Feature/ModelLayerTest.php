<?php

namespace Tests\Feature;

use App\Enums\AnalyticsEventType;
use App\Enums\ContactMessageStatus;
use App\Enums\ProjectMediaType;
use App\Enums\PublicationStatus;
use App\Enums\TestimonialStatus;
use App\Models\AnalyticsEvent;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectLike;
use App\Models\ProjectMedia;
use App\Models\ProjectView;
use App\Models\SeoMetadata;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ModelLayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_localized_attribute_helper_returns_requested_locale_and_fallback(): void
    {
        $project = Project::factory()->make([
            'title' => ['en' => 'English title'],
            'summary' => ['en' => 'English summary', 'ar' => 'ملخص عربي'],
        ]);

        $this->assertSame('ملخص عربي', $project->localized('summary', 'ar'));
        $this->assertSame('English title', $project->localized('title', 'ar'));
        $this->assertSame(['en' => 'English title'], $project->localizedValues('title'));
    }

    public function test_project_relationships_and_scopes_are_available(): void
    {
        $category = ProjectCategory::factory()->create();
        $technology = Technology::factory()->create();
        $project = Project::factory()
            ->for($category, 'category')
            ->published()
            ->featured()
            ->withMedia()
            ->create();

        $project->technologies()->attach($technology);
        Testimonial::factory()->approved()->for($project)->create();
        ProjectView::factory()->for($project)->create();
        ProjectLike::factory()->for($project)->create();

        $this->assertTrue(Project::published()->whereKey($project)->exists());
        $this->assertTrue(Project::featured()->whereKey($project)->exists());
        $this->assertTrue(Project::byCategory($category)->whereKey($project)->exists());
        $this->assertTrue(Project::byTechnology($technology)->whereKey($project)->exists());
        $this->assertTrue($project->category->is($category));
        $this->assertCount(1, $project->technologies);
        $this->assertCount(1, $project->media);
        $this->assertCount(1, $project->testimonials);
        $this->assertCount(1, $project->views);
        $this->assertCount(1, $project->likes);
    }

    public function test_blog_and_polymorphic_seo_relationships_are_available(): void
    {
        $post = BlogPost::factory()->published()->create();
        $seo = SeoMetadata::factory()->for($post, 'seoable')->create();

        $this->assertTrue(BlogPost::published()->whereKey($post)->exists());
        $this->assertTrue($post->category()->exists());
        $this->assertTrue($post->seoMetadata->is($seo));
        $this->assertTrue($seo->seoable->is($post));
    }

    public function test_enum_casts_match_database_values(): void
    {
        $project = Project::factory()->published()->create();
        $media = ProjectMedia::factory()->video()->create();
        $testimonial = Testimonial::factory()->approved()->create();
        $contact = ContactMessage::factory()->read()->create();
        $event = AnalyticsEvent::factory()->create(['event_type' => AnalyticsEventType::ProjectView]);

        $this->assertSame(PublicationStatus::Published, $project->status);
        $this->assertSame(ProjectMediaType::Video, $media->type);
        $this->assertSame(TestimonialStatus::Approved, $testimonial->status);
        $this->assertSame(ContactMessageStatus::Read, $contact->status);
        $this->assertSame(AnalyticsEventType::ProjectView, $event->event_type);
        $this->assertSame('Published', PublicationStatus::Published->label());
    }

    public function test_sensitive_attributes_are_hidden_from_default_serialization(): void
    {
        $view = ProjectView::factory()->create();
        $like = ProjectLike::factory()->create();
        $event = AnalyticsEvent::factory()->create();
        $testimonial = Testimonial::factory()->create();
        $contact = ContactMessage::factory()->create(['admin_notes' => 'Private note']);

        $this->assertArrayNotHasKey('visitor_id_hash', $view->toArray());
        $this->assertArrayNotHasKey('ip_hash', $like->toArray());
        $this->assertArrayNotHasKey('user_agent_hash', $event->toArray());
        $this->assertArrayNotHasKey('contact_email', $testimonial->toArray());
        $this->assertArrayNotHasKey('email', $contact->toArray());
        $this->assertArrayNotHasKey('admin_notes', $contact->toArray());
    }

    public function test_dashboard_policy_requires_explicit_administrator(): void
    {
        $ordinaryUser = User::factory()->create();
        $admin = User::factory()->administrator()->create();
        $project = Project::factory()->create();

        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('viewAny', Project::class));
        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('create', Project::class));
        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('update', $project));
        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Project::class));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Project::class));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $project));
        $this->assertFalse(Gate::allows('update', $project));
    }
}
