<?php

namespace App\Services\Admin\Analytics;

use App\Models\AnalyticsEvent;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class AnalyticsEventCleanupService
{
    public function prune(?int $retentionDays = null, CarbonInterface|string|null $now = null): int
    {
        $retentionDays = max(1, $retentionDays ?? (int) config('portfolio.analytics.raw_event_retention_days', 180));
        $cutoff = CarbonImmutable::parse($now ?? now())->subDays($retentionDays)->startOfDay();

        return AnalyticsEvent::query()
            ->where('occurred_at', '<', $cutoff)
            ->delete();
    }
}
