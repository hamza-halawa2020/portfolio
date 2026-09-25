<?php

namespace App\Jobs\Analytics;

use App\Services\Admin\Analytics\AnalyticsEventCleanupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupAnalyticsEventsJob implements ShouldQueue
{
    use Queueable;

    public function handle(AnalyticsEventCleanupService $cleanup): void
    {
        $cleanup->prune();
    }
}
