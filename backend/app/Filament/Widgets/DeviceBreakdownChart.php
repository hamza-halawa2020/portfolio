<?php

namespace App\Filament\Widgets;

use App\Queries\Admin\AnalyticsDashboardQuery;
use Filament\Widgets\ChartWidget;

class DeviceBreakdownChart extends ChartWidget
{
    protected ?string $heading = 'Device breakdown';

    protected function getData(): array
    {
        $rows = app(AnalyticsDashboardQuery::class)->breakdown('device_category');

        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => $rows->pluck('value')->all(),
                ],
            ],
            'labels' => $rows->pluck('label')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
