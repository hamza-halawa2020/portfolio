<?php

namespace App\Services\Admin\Analytics;

use App\Enums\AnalyticsEventType;
use App\Models\AnalyticsEvent;
use App\Models\ContactMessage;
use App\Models\DailyAnalyticsSummary;
use App\Models\ProjectLike;
use App\Models\ProjectView;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class DailyAnalyticsAggregator
{
    /**
     * @return array<string, int>
     */
    public function aggregate(CarbonInterface|string|null $date = null): array
    {
        $date = CarbonImmutable::parse($date ?? now()->subDay())->startOfDay();
        $from = $date->startOfDay();
        $to = $date->endOfDay();

        $metrics = [
            'page_views' => AnalyticsEvent::query()
                ->where('event_type', AnalyticsEventType::PageView)
                ->whereBetween('occurred_at', [$from, $to])
                ->count(),
            'unique_visitors' => AnalyticsEvent::query()
                ->whereNotNull('visitor_id_hash')
                ->whereBetween('occurred_at', [$from, $to])
                ->distinct('visitor_id_hash')
                ->count('visitor_id_hash'),
            'project_views' => ProjectView::query()
                ->whereBetween('viewed_at', [$from, $to])
                ->count(),
            'project_likes' => ProjectLike::query()
                ->whereBetween('liked_at', [$from, $to])
                ->count(),
            'contact_submissions' => ContactMessage::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),
        ];

        foreach ($metrics as $metric => $value) {
            DailyAnalyticsSummary::query()->updateOrCreate(
                [
                    'date' => $date->toDateString(),
                    'metric' => $metric,
                    'dimension' => null,
                ],
                ['value' => $value],
            );
        }

        return $metrics;
    }
}
