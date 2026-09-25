<?php

namespace App\Queries\Admin;

use App\Enums\AnalyticsEventType;
use App\Enums\ContactMessageStatus;
use App\Enums\TestimonialStatus;
use App\Models\AnalyticsEvent;
use App\Models\ContactMessage;
use App\Models\DailyAnalyticsSummary;
use App\Models\Project;
use App\Models\ProjectLike;
use App\Models\ProjectView;
use App\Models\Testimonial;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboardQuery
{
    /**
     * @return array<string, int>
     */
    public function totals(?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        [$from, $to] = $this->range($from, $to);

        return [
            'total_visits' => $this->eventsBetween($from, $to)
                ->where('event_type', AnalyticsEventType::PageView)
                ->count(),
            'unique_visitors' => $this->eventsBetween($from, $to)
                ->whereNotNull('visitor_id_hash')
                ->distinct('visitor_id_hash')
                ->count('visitor_id_hash'),
            'project_views' => $this->projectViewsBetween($from, $to)->count(),
            'project_likes' => $this->projectLikesBetween($from, $to)->count(),
            'contact_submissions' => ContactMessage::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),
            'pending_testimonials' => Testimonial::query()
                ->where('status', TestimonialStatus::Pending)
                ->count(),
        ];
    }

    /**
     * @return Collection<int, array{date: string, visits: int, unique_visitors: int, project_views: int, contacts: int}>
     */
    public function dailyTrends(?CarbonInterface $from = null, ?CarbonInterface $to = null): Collection
    {
        [$from, $to] = $this->range($from, $to);
        $days = collect();

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            $dayStart = $day->startOfDay();
            $dayEnd = $day->endOfDay();
            $summary = $this->dailySummary($dayStart);

            $days->push([
                'date' => $dayStart->toDateString(),
                'visits' => $summary['page_views'] ?? $this->eventsBetween($dayStart, $dayEnd)->where('event_type', AnalyticsEventType::PageView)->count(),
                'unique_visitors' => $summary['unique_visitors'] ?? $this->eventsBetween($dayStart, $dayEnd)->whereNotNull('visitor_id_hash')->distinct('visitor_id_hash')->count('visitor_id_hash'),
                'project_views' => $summary['project_views'] ?? $this->projectViewsBetween($dayStart, $dayEnd)->count(),
                'contacts' => $summary['contact_submissions'] ?? ContactMessage::query()->whereBetween('created_at', [$dayStart, $dayEnd])->count(),
            ]);
        }

        return $days;
    }

    /**
     * @return Collection<int, array{title: string, views: int, likes: int}>
     */
    public function topProjects(?CarbonInterface $from = null, ?CarbonInterface $to = null, int $limit = 5): Collection
    {
        [$from, $to] = $this->range($from, $to);

        return Project::query()
            ->withCount([
                'views as views_count' => fn ($query) => $query->whereBetween('viewed_at', [$from, $to]),
                'likes as likes_count' => fn ($query) => $query->whereBetween('liked_at', [$from, $to]),
            ])
            ->orderByDesc('views_count')
            ->orderByDesc('likes_count')
            ->limit($limit)
            ->get()
            ->map(fn (Project $project): array => [
                'title' => $project->localized('title', 'en'),
                'views' => (int) $project->views_count,
                'likes' => (int) $project->likes_count,
            ]);
    }

    /**
     * @return Collection<int, array{label: string, value: int}>
     */
    public function breakdown(string $column, ?CarbonInterface $from = null, ?CarbonInterface $to = null, int $limit = 6): Collection
    {
        [$from, $to] = $this->range($from, $to);

        if (! in_array($column, ['device_category', 'referrer_domain', 'browser', 'country'], true)) {
            return collect();
        }

        return $this->eventsBetween($from, $to)
            ->select($column, DB::raw('count(*) as aggregate'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get()
            ->map(fn (AnalyticsEvent $event): array => [
                'label' => (string) $event->{$column},
                'value' => (int) $event->aggregate,
            ]);
    }

    /**
     * @return array<string, int>
     */
    public function contactStatusCounts(?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        [$from, $to] = $this->range($from, $to);

        return collect(ContactMessageStatus::cases())
            ->mapWithKeys(fn (ContactMessageStatus $status): array => [
                $status->value => ContactMessage::query()
                    ->where('status', $status)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ])
            ->all();
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function range(?CarbonInterface $from, ?CarbonInterface $to): array
    {
        $to = CarbonImmutable::parse($to ?? now())->endOfDay();
        $from = CarbonImmutable::parse($from ?? $to->subDays(29))->startOfDay();

        return [$from, $to];
    }

    private function eventsBetween(CarbonInterface $from, CarbonInterface $to): Builder
    {
        return AnalyticsEvent::query()->whereBetween('occurred_at', [$from, $to]);
    }

    private function projectViewsBetween(CarbonInterface $from, CarbonInterface $to): Builder
    {
        return ProjectView::query()->whereBetween('viewed_at', [$from, $to]);
    }

    private function projectLikesBetween(CarbonInterface $from, CarbonInterface $to): Builder
    {
        return ProjectLike::query()->whereBetween('liked_at', [$from, $to]);
    }

    /**
     * @return array<string, int>
     */
    private function dailySummary(CarbonInterface $date): array
    {
        $rows = DailyAnalyticsSummary::query()
            ->whereDate('date', $date->toDateString())
            ->whereNull('dimension')
            ->whereIn('metric', ['page_views', 'unique_visitors', 'project_views', 'contact_submissions'])
            ->get(['metric', 'value']);

        if ($rows->count() < 4) {
            return [];
        }

        return $rows
            ->mapWithKeys(fn (DailyAnalyticsSummary $summary): array => [$summary->metric => (int) $summary->value])
            ->all();
    }
}
