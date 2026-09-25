<?php

namespace Tests\Feature;

use App\Data\PublicApi\VisitorContext;
use App\Enums\ContactMessageStatus;
use App\Enums\PublicationStatus;
use App\Enums\TestimonialStatus;
use App\Events\PublicApi\ContactMessageSubmitted;
use App\Events\PublicApi\TestimonialSubmitted;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Models\ProjectView;
use App\Models\Testimonial;
use App\Services\PublicApi\LikeProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicWriteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['portfolio.visitor.hash_secret' => 'test-visitor-secret']);
        RateLimiter::clear('public-write-test');
    }

    public function test_first_project_view_is_counted_and_repeat_inside_window_is_idempotent(): void
    {
        $project = Project::factory()->published()->create(['slug' => ['en' => 'viewed-project']]);
        $visitor = (string) Str::uuid();

        $first = $this->withVisitorCookie($visitor)->postJson('/api/v1/projects/viewed-project/views');
        $second = $this->withVisitorCookie($visitor)->postJson('/api/v1/projects/viewed-project/views');

        $first->assertCreated()
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.created', true);

        $second->assertOk()
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.created', false)
            ->assertJsonMissing(['visitor_id_hash'])
            ->assertJsonMissing(['ip_hash'])
            ->assertJsonMissing(['user_agent_hash']);

        $this->assertSame(1, ProjectView::query()->where('project_id', $project->id)->count());
    }

    public function test_different_visitors_and_next_utc_day_project_views_are_counted(): void
    {
        $project = Project::factory()->published()->create([
            'slug' => ['en' => 'daily-project'],
            'published_at' => '2026-09-17 10:00:00',
        ]);
        $firstVisitor = (string) Str::uuid();
        $secondVisitor = (string) Str::uuid();

        Carbon::setTestNow('2026-09-18 10:00:00');
        $this->withVisitorCookie($firstVisitor)->postJson('/api/v1/projects/daily-project/views')->assertCreated();
        $this->withVisitorCookie($secondVisitor)->postJson('/api/v1/projects/daily-project/views')->assertCreated();

        Carbon::setTestNow('2026-09-19 10:00:00');
        $this->withVisitorCookie($firstVisitor)
            ->postJson('/api/v1/projects/daily-project/views')
            ->assertCreated()
            ->assertJsonPath('data.count', 3);

        Carbon::setTestNow();

        $this->assertSame(3, ProjectView::query()->where('project_id', $project->id)->count());
    }

    public function test_project_view_for_draft_project_returns_404(): void
    {
        Project::factory()->create(['slug' => ['en' => 'draft-project'], 'status' => PublicationStatus::Draft]);

        $this->postJson('/api/v1/projects/draft-project/views')->assertNotFound();
    }

    public function test_like_and_unlike_are_idempotent_and_authoritative(): void
    {
        $project = Project::factory()->published()->create(['slug' => ['en' => 'liked-project']]);
        $visitor = (string) Str::uuid();

        $this->withVisitorCookie($visitor)
            ->postJson('/api/v1/projects/liked-project/likes')
            ->assertCreated()
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.liked', true);

        $this->withVisitorCookie($visitor)
            ->postJson('/api/v1/projects/liked-project/likes')
            ->assertOk()
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.liked', true);

        $this->withVisitorCookie($visitor)
            ->deleteJson('/api/v1/projects/liked-project/likes')
            ->assertOk()
            ->assertJsonPath('data.count', 0)
            ->assertJsonPath('data.liked', false);

        $this->withVisitorCookie($visitor)
            ->deleteJson('/api/v1/projects/liked-project/likes')
            ->assertOk()
            ->assertJsonPath('data.count', 0)
            ->assertJsonPath('data.liked', false);

        $this->assertSame(0, ProjectLike::query()->where('project_id', $project->id)->count());
    }

    public function test_like_state_is_resolved_from_visitor_cookie_without_exposing_identifiers(): void
    {
        Project::factory()->published()->create(['slug' => ['en' => 'stateful-project']]);
        $visitor = (string) Str::uuid();

        $this->withVisitorCookie($visitor)
            ->getJson('/api/v1/projects/stateful-project/likes')
            ->assertOk()
            ->assertJsonPath('data.count', 0)
            ->assertJsonPath('data.liked', false)
            ->assertJsonMissing(['visitor_id_hash']);

        $this->withVisitorCookie($visitor)->postJson('/api/v1/projects/stateful-project/likes')->assertCreated();

        $this->withVisitorCookie($visitor)
            ->getJson('/api/v1/projects/stateful-project/likes')
            ->assertOk()
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.liked', true)
            ->assertJsonMissing(['ip_hash']);

        $this->assertStringContainsString('no-store', $this->withVisitorCookie($visitor)->getJson('/api/v1/projects/stateful-project/likes')->headers->get('Cache-Control'));
    }

    public function test_different_visitors_can_like_independently_and_draft_project_returns_404(): void
    {
        Project::factory()->published()->create(['slug' => ['en' => 'popular-project']]);
        Project::factory()->create(['slug' => ['en' => 'draft-like'], 'status' => PublicationStatus::Draft]);

        $this->withVisitorCookie((string) Str::uuid())
            ->postJson('/api/v1/projects/popular-project/likes')
            ->assertCreated()
            ->assertJsonPath('data.count', 1);

        $this->withVisitorCookie((string) Str::uuid())
            ->postJson('/api/v1/projects/popular-project/likes')
            ->assertCreated()
            ->assertJsonPath('data.count', 2)
            ->assertJsonMissing(['visitor_id_hash']);

        $this->postJson('/api/v1/projects/draft-like/likes')->assertNotFound();
    }

    public function test_valid_testimonial_submission_is_private_pending_and_acknowledged(): void
    {
        Event::fake([TestimonialSubmitted::class]);
        Project::factory()->published()->create(['slug' => ['en' => 'client-project']]);

        $response = $this->postJson('/api/v1/testimonials', [
            'name' => 'Client Person',
            'company' => 'Fictional Co',
            'position' => 'Founder',
            'content' => 'This was thoughtful, reliable, and very well delivered.',
            'rating' => 5,
            'contact_email' => 'CLIENT@Example.test',
            'project' => 'client-project',
            'publication_consent' => true,
            'status' => 'approved',
            'is_featured' => true,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['status', 'is_featured']);

        $response = $this->postJson('/api/v1/testimonials', [
            'name' => 'Client Person',
            'company' => 'Fictional Co',
            'position' => 'Founder',
            'content' => 'This was thoughtful, reliable, and very well delivered.',
            'rating' => 5,
            'contact_email' => 'CLIENT@Example.test',
            'project' => 'client-project',
            'publication_consent' => true,
        ]);

        $response->assertAccepted()
            ->assertJsonPath('data.message', 'Submission received successfully.')
            ->assertJsonMissing(['contact_email'])
            ->assertJsonMissing(['status'])
            ->assertJsonMissing(['is_featured']);

        $testimonial = Testimonial::query()->firstOrFail();
        $this->assertSame(TestimonialStatus::Pending, $testimonial->status);
        $this->assertFalse($testimonial->is_featured);
        $this->assertSame('client@example.test', $testimonial->contact_email);

        $this->getJson('/api/v1/testimonials')->assertJsonPath('meta.total', 0);
        Event::assertDispatched(TestimonialSubmitted::class);
    }

    public function test_testimonial_requires_consent_rejects_invalid_project_and_honeypot(): void
    {
        Project::factory()->create(['slug' => ['en' => 'draft-testimonial'], 'status' => PublicationStatus::Draft]);

        $payload = [
            'name' => 'Client Person',
            'content' => 'The project was delivered very carefully and professionally.',
            'contact_email' => 'client@example.test',
        ];

        $this->postJson('/api/v1/testimonials', $payload)->assertUnprocessable()->assertJsonValidationErrors('publication_consent');
        $this->postJson('/api/v1/testimonials', [...$payload, 'publication_consent' => true, 'project' => 'draft-testimonial'])->assertUnprocessable()->assertJsonValidationErrors('project');
        $this->postJson('/api/v1/testimonials', [...$payload, 'publication_consent' => true, 'website' => 'spam'])->assertUnprocessable()->assertJsonValidationErrors('website');
    }

    public function test_valid_contact_submission_is_private_new_and_acknowledged(): void
    {
        Event::fake([ContactMessageSubmitted::class]);

        $response = $this->postJson('/api/v1/contact', [
            'name' => 'Prospect Person',
            'email' => 'PROSPECT@Example.test',
            'phone' => '+1 (555) 010-1234',
            'company' => 'Example Studio',
            'project_type' => 'Portfolio',
            'budget_range' => '5000-10000',
            'message' => 'I would like to discuss a fictional portfolio project.',
            'privacy_consent' => true,
            'status' => 'archived',
            'admin_notes' => 'should not pass',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['status', 'admin_notes']);

        $response = $this->postJson('/api/v1/contact', [
            'name' => 'Prospect Person',
            'email' => 'PROSPECT@Example.test',
            'phone' => '+1 (555) 010-1234',
            'company' => 'Example Studio',
            'project_type' => 'Portfolio',
            'budget_range' => '5000-10000',
            'message' => 'I would like to discuss a fictional portfolio project.',
            'privacy_consent' => true,
        ]);

        $response->assertAccepted()
            ->assertJsonPath('data.message', 'Submission received successfully.')
            ->assertJsonMissing(['email'])
            ->assertJsonMissing(['message'])
            ->assertJsonMissing(['admin_notes']);

        $message = ContactMessage::query()->firstOrFail();
        $this->assertSame(ContactMessageStatus::New, $message->status);
        $this->assertSame('prospect@example.test', $message->email);

        Event::assertDispatched(ContactMessageSubmitted::class);
    }

    public function test_contact_requires_consent_rejects_invalid_payload_honeypot_and_attachment(): void
    {
        $payload = [
            'name' => 'Prospect Person',
            'email' => 'prospect@example.test',
            'message' => 'I would like to discuss a fictional portfolio project.',
        ];

        $this->postJson('/api/v1/contact', $payload)->assertUnprocessable()->assertJsonValidationErrors('privacy_consent');
        $this->postJson('/api/v1/contact', [...$payload, 'privacy_consent' => true, 'message' => 'short'])->assertUnprocessable()->assertJsonValidationErrors('message');
        $this->postJson('/api/v1/contact', [...$payload, 'privacy_consent' => true, 'website' => 'spam'])->assertUnprocessable()->assertJsonValidationErrors('website');
        $this->postJson('/api/v1/contact', [...$payload, 'privacy_consent' => true, 'attachment' => 'not-yet-supported'])->assertUnprocessable()->assertJsonValidationErrors('attachment');
    }

    public function test_write_rate_limiters_are_endpoint_specific(): void
    {
        $visitor = (string) Str::uuid();
        $payload = [
            'name' => 'Prospect Person',
            'email' => 'prospect@example.test',
            'message' => 'I would like to discuss a fictional portfolio project.',
            'privacy_consent' => true,
        ];

        $this->withVisitorCookie($visitor)->postJson('/api/v1/contact', $payload)->assertAccepted();

        for ($i = 1; $i <= (int) config('portfolio.rate_limits.contact', 5); $i++) {
            $response = $this->withVisitorCookie($visitor)->postJson('/api/v1/contact', [
                ...$payload,
                'email' => "another{$i}@example.test",
                'message' => "A fictional message number {$i} should approach the limiter.",
            ]);
        }

        $response->assertTooManyRequests();
    }

    public function test_project_like_service_works_without_http_request_state(): void
    {
        $project = Project::factory()->published()->create(['slug' => ['en' => 'service-liked-project']]);
        $visitor = new VisitorContext(
            visitorIdHash: hash('sha256', 'service-visitor'),
            ipHash: null,
            userAgentHash: null,
            issuedCookie: false,
            rateLimitKey: 'service-test',
            cookieValue: 'test-cookie',
        );

        $result = app(LikeProject::class)->handle('service-liked-project', 'en', $visitor);

        $this->assertTrue($result->created);
        $this->assertTrue($result->liked);
        $this->assertSame(1, $result->count);
        $this->assertSame(1, ProjectLike::query()->where('project_id', $project->id)->count());
    }

    private function visitorCookieName(): string
    {
        return (string) config('portfolio.visitor.cookie', 'portfolio_visitor');
    }

    private function withVisitorCookie(string $visitor): self
    {
        return $this->withCredentials()->withUnencryptedCookie($this->visitorCookieName(), Crypt::encryptString($visitor));
    }
}
