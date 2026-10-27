<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\ChartWidget;

class VisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Bezoeken per dag (afgelopen 30 dagen)';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $viewsPerDay = PageView::query()
            ->selectRaw('date(created_at) as day, count(*) as views')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('day')
            ->pluck('views', 'day');

        $labels = [];
        $values = [];

        for ($date = now()->subDays(29)->startOfDay(); $date <= now(); $date = $date->addDay()) {
            $key = $date->toDateString();
            $labels[] = $date->format('d-m');
            $values[] = (int) ($viewsPerDay[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bezoeken',
                    'data' => $values,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
