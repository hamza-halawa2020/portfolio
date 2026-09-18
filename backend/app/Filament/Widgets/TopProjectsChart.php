<?php

namespace App\Filament\Widgets;

use App\Queries\Admin\AnalyticsDashboardQuery;
use Filament\Widgets\ChartWidget;

class TopProjectsChart extends ChartWidget
{
    protected ?string $heading = 'Most viewed and liked projects';

    protected function getData(): array
    {
        $rows = app(AnalyticsDashboardQuery::class)->topProjects();

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $rows->pluck('views')->all(),
                ],
                [
                    'label' => 'Likes',
                    'data' => $rows->pluck('likes')->all(),
                ],
            ],
            'labels' => $rows->pluck('title')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
