<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VisitsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Bezoeken vandaag', number_format(
                PageView::query()->where('created_at', '>=', now()->startOfDay())->count(),
                0, ',', '.'
            )),
            Stat::make('Bezoeken afgelopen 7 dagen', number_format(
                PageView::query()->where('created_at', '>=', now()->subDays(7))->count(),
                0, ',', '.'
            )),
            Stat::make('Bezoeken afgelopen 30 dagen', number_format(
                PageView::query()->where('created_at', '>=', now()->subDays(30))->count(),
                0, ',', '.'
            )),
            Stat::make('Unieke bezoekers afgelopen 30 dagen', number_format(
                PageView::query()->where('created_at', '>=', now()->subDays(30))->distinct('visitor_hash')->count('visitor_hash'),
                0, ',', '.'
            ))->description('Op basis van anonieme dag-hash'),
        ];
    }
}
