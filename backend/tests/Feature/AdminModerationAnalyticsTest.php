<?php

namespace Tests\Feature;

use App\Enums\AnalyticsEventType;
use App\Enums\ContactMessageStatus;
use App\Enums\TestimonialStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Models\AnalyticsEvent;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Models\ProjectView;
use App\Models\Testimonial;
use App\Models\User;
use App\Queries\Admin\AnalyticsDashboardQuery;
use App\Services\Admin\Content\ContactInboxService;
use App\Services\Admin\Content\TestimonialModerationService;
use Carbon\CarbonImmutable;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminModerationAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_access_to_moderation_inbox_and_analytics_resources(): void
    {
        $admin = User::factory()->administrator()->create();
        $ordinaryUser = User::factory()->create();

        $this->actingAs($admin)->get('/admin/testimonials')->assertOk();
        $this->actingAs($admin)->get('/admin/contact-messages')->assertOk();
        $this->actingAs($admin)->get('/admin')->assertOk();

        $this->actingAs($ordinaryUser)->get('/admin/testimonials')->assertForbidden();
        $this->actingAs($ordinaryUser)->get('/admin/contact-messages')->assertForbidden();

        $this->assertContains(TestimonialResource::class, Filament::getPanel('admin')->getResources());
        $this->assertContains(ContactMessageResource::class, Filament::getPanel('admin')->getResources());
        $this->assertTrue(Gate::forUser($admin)->allows('update', Testimonial::factory()->create()));
        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('viewAny', ContactMessage::class));
    }

    public function test_testimonial_moderation_controls_public_visibility(): void
    {
        $project = Project::factory()->published()->create();
        $approved = Testimonial::factory()->for($project)->create([
            'content' => ['en' => 'Approved fixture', 'ar' => 'شهادة مقبولة'],
        ]);
        $pending = Testimonial::factory()->for($project)->create();
        $rejected = Testimonial::factory()->for($project)->create();

        app(TestimonialModerationService::class)->approve($approved);
        app(TestimonialModerationService::class)->reject($rejected);

        $this->getJson('/api/v1/testimonials?locale=en')
            ->assertOk()
            ->assertJsonFragment(['content' => 'Approved fixture'])
            ->assertJsonMissing(['id' => $pending->id])
            ->assertJsonMissing(['id' => $rejected->id]);

        $this->assertSame(TestimonialStatus::Approved, $approved->refresh()->status);
        $this->assertNotNull($approved->reviewed_at);
        $this->assertSame(TestimonialStatus::Rejected, $rejected->refresh()->status);
    }

    public function test_contact_inbox_status_and_private_notes_are_updated_through_service(): void
    {
        $message = ContactMessage::factory()->create([
            'status' => ContactMessageStatus::New,
            'admin_notes' => null,
        ]);

        app(ContactInboxService::class)->markRead($message);

        $this->assertSame(ContactMessageStatus::Read, $message->refresh()->status);

        app(ContactInboxService::class)->update($message, [
            'status' => ContactMessageStatus::Replied,
            'admin_notes' => 'Private dashboard note.',
        ]);

        $this->assertSame(ContactMessageStatus::Replied, $message->refresh()->status);
        $this->assertSame('Private dashboard note.', $message->admin_notes);
        $this->getJson('/api/v1/site')->assertJsonMissing(['admin_notes' => 'Private dashboard note.']);
    }

    public function test_analytics_aggregation_respects_date_boundaries_and_distinguishes_metrics(): void
    {
        $project = Project::factory()->published()->create(['title' => ['en' => 'Boundary Project']]);
        $inside = CarbonImmutable::parse('2026-09-10 12:00:00');
        $outside = CarbonImmutable::parse('2026-08-01 12:00:00');

        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'visitor-a'),
            'occurred_at' => $inside,
            'device_category' => 'desktop',
        ]);
        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'visitor-a'),
            'occurred_at' => $inside->addHour(),
            'device_category' => 'desktop',
        ]);
        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'visitor-old'),
            'occurred_at' => $outside,
        ]);
        ProjectView::factory()->for($project)->create(['viewed_at' => $inside, 'viewed_on' => $inside->toDateString()]);
        ProjectLike::factory()->for($project)->create(['liked_at' => $inside]);
        ContactMessage::factory()->create(['created_at' => $inside]);
        Testimonial::factory()->create(['status' => TestimonialStatus::Pending]);

        $totals = app(AnalyticsDashboardQuery::class)->totals($inside->startOfDay(), $inside->endOfDay());

        $this->assertSame(2, $totals['total_visits']);
        $this->assertSame(1, $totals['unique_visitors']);
        $this->assertSame(1, $totals['project_views']);
        $this->assertSame(1, $totals['project_likes']);
        $this->assertSame(1, $totals['contact_submissions']);
        $this->assertSame(1, $totals['pending_testimonials']);
        $this->assertSame('Boundary Project', app(AnalyticsDashboardQuery::class)->topProjects($inside->startOfDay(), $inside->endOfDay())->first()['title']);
        $this->assertSame('desktop', app(AnalyticsDashboardQuery::class)->breakdown('device_category', $inside->startOfDay(), $inside->endOfDay())->first()['label']);
    }

    public function test_analytics_widgets_delegate_queries_to_query_service(): void
    {
        foreach (glob(app_path('Filament/Widgets/*.php')) as $file) {
            $source = file_get_contents($file);

            $this->assertStringContainsString('AnalyticsDashboardQuery::class', $source, $file);
            $this->assertStringNotContainsString('::query(', $source, $file);
            $this->assertStringNotContainsString('App\\Models\\', $source, $file);
        }
    }
}
