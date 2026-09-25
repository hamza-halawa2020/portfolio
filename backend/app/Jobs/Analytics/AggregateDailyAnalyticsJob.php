<?php

namespace App\Jobs\Analytics;

use App\Services\Admin\Analytics\DailyAnalyticsAggregator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AggregateDailyAnalyticsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ?string $date = null) {}

    public function handle(DailyAnalyticsAggregator $aggregator): void
    {
        $aggregator->aggregate($this->date);
    }
}
