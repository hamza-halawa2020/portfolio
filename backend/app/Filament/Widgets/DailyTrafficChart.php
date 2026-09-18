<?php

namespace App\Filament\Widgets;

use App\Queries\Admin\AnalyticsDashboardQuery;
use Filament\Widgets\ChartWidget;

class DailyTrafficChart extends ChartWidget
{
    protected ?string $heading = 'Daily trends';

    protected function getData(): array
    {
        $rows = app(AnalyticsDashboardQuery::class)->dailyTrends();

        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => $rows->pluck('visits')->all(),
                ],
                [
                    'label' => 'Unique visitors',
                    'data' => $rows->pluck('unique_visitors')->all(),
                ],
                [
                    'label' => 'Project views',
                    'data' => $rows->pluck('project_views')->all(),
                ],
                [
                    'label' => 'Contacts',
                    'data' => $rows->pluck('contacts')->all(),
                ],
            ],
            'labels' => $rows->pluck('date')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
