<?php

namespace App\Filament\Widgets;

use App\Queries\Admin\AnalyticsDashboardQuery;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Analytics overview';

    protected function getStats(): array
    {
        $totals = app(AnalyticsDashboardQuery::class)->totals();

        return [
            Stat::make('Total visits', number_format($totals['total_visits']))
                ->description('Page-view analytics events'),
            Stat::make('Unique visitors', number_format($totals['unique_visitors']))
                ->description('Distinct hashed visitors'),
            Stat::make('Project views', number_format($totals['project_views']))
                ->description('Project detail view records'),
            Stat::make('Project likes', number_format($totals['project_likes']))
                ->description('Current project likes'),
            Stat::make('Contact submissions', number_format($totals['contact_submissions']))
                ->description('Private inbox messages'),
            Stat::make('Pending testimonials', number_format($totals['pending_testimonials']))
                ->description('Waiting for moderation'),
        ];
    }
}
