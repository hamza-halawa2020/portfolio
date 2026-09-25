<?php

namespace Tests\Feature;

use App\Enums\AnalyticsEventType;
use App\Jobs\Analytics\AggregateDailyAnalyticsJob;
use App\Jobs\Analytics\CleanupAnalyticsEventsJob;
use App\Models\AnalyticsEvent;
use App\Models\ContactMessage;
use App\Models\DailyAnalyticsSummary;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Models\ProjectView;
use App\Queries\Admin\AnalyticsDashboardQuery;
use App\Services\Admin\Analytics\AnalyticsEventCleanupService;
use App\Services\Admin\Analytics\DailyAnalyticsAggregator;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnalyticsAggregationCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_analytics_job_writes_privacy_safe_summaries(): void
    {
        $date = CarbonImmutable::parse('2026-09-20 12:00:00');
        $project = Project::factory()->published()->create();

        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'summary-visitor-a'),
            'occurred_at' => $date,
        ]);
        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'summary-visitor-a'),
            'occurred_at' => $date->addHour(),
        ]);
        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'summary-visitor-old'),
            'occurred_at' => $date->subDays(2),
        ]);
        ProjectView::factory()->for($project)->create(['viewed_at' => $date, 'viewed_on' => $date->toDateString()]);
        ProjectLike::factory()->for($project)->create(['liked_at' => $date]);
        ContactMessage::factory()->create(['created_at' => $date]);

        (new AggregateDailyAnalyticsJob($date->toDateString()))->handle(app(DailyAnalyticsAggregator::class));

        $this->assertSame([
            'contact_submissions' => 1,
            'page_views' => 2,
            'project_likes' => 1,
            'project_views' => 1,
            'unique_visitors' => 1,
        ], DailyAnalyticsSummary::query()
            ->whereDate('date', $date->toDateString())
            ->whereNull('dimension')
            ->pluck('value', 'metric')
            ->sortKeys()
            ->all());

        $this->assertFalse(Schema::hasColumn('analytics_daily_summaries', 'visitor_id_hash'));
        $this->assertFalse(Schema::hasColumn('analytics_daily_summaries', 'ip_hash'));
        $this->assertFalse(Schema::hasColumn('analytics_daily_summaries', 'user_agent_hash'));
    }

    public function test_dashboard_daily_trends_use_summaries_when_available(): void
    {
        $date = CarbonImmutable::parse('2026-09-21 12:00:00');

        foreach ([
            'page_views' => 7,
            'unique_visitors' => 3,
            'project_views' => 5,
            'contact_submissions' => 2,
        ] as $metric => $value) {
            DailyAnalyticsSummary::factory()->create([
                'date' => $date->toDateString(),
                'metric' => $metric,
                'dimension' => null,
                'value' => $value,
            ]);
        }

        AnalyticsEvent::factory()->create([
            'event_type' => AnalyticsEventType::PageView,
            'visitor_id_hash' => hash('sha256', 'live-value-not-used'),
            'occurred_at' => $date,
        ]);

        $row = app(AnalyticsDashboardQuery::class)
            ->dailyTrends($date->startOfDay(), $date->endOfDay())
            ->first();

        $this->assertSame([
            'date' => $date->toDateString(),
            'visits' => 7,
            'unique_visitors' => 3,
            'project_views' => 5,
            'contacts' => 2,
        ], $row);
    }

    public function test_cleanup_job_prunes_only_expired_raw_analytics_events(): void
    {
        config(['portfolio.analytics.raw_event_retention_days' => 30]);

        $now = CarbonImmutable::parse('2026-09-25 12:00:00');
        $expired = AnalyticsEvent::factory()->create(['occurred_at' => $now->subDays(45)]);
        $retained = AnalyticsEvent::factory()->create(['occurred_at' => $now->subDays(5)]);

        $cleanup = new class($now) extends AnalyticsEventCleanupService
        {
            public function __construct(private CarbonImmutable $now) {}

            public function prune(?int $retentionDays = null, CarbonInterface|string|null $now = null): int
            {
                return parent::prune($retentionDays, $now ?? $this->now);
            }
        };

        (new CleanupAnalyticsEventsJob)->handle($cleanup);

        $this->assertDatabaseMissing('analytics_events', ['id' => $expired->id]);
        $this->assertDatabaseHas('analytics_events', ['id' => $retained->id]);
    }

    public function test_analytics_jobs_are_scheduled(): void
    {
        Artisan::call('schedule:list');

        $output = Artisan::output();

        $this->assertStringContainsString('analytics.aggregate-daily', $output);
        $this->assertStringContainsString('analytics.cleanup-events', $output);
    }
}
