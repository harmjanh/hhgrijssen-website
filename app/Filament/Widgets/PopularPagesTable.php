<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularPagesTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Populairste pagina\'s (afgelopen 30 dagen)';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageView::query()
                    ->selectRaw('min(id) as id, path, count(*) as views, count(distinct visitor_hash) as unique_visitors')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('path')
            )
            ->defaultSort('views', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('Pagina')
                    ->searchable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Bezoeken')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_visitors')
                    ->label('Unieke bezoekers')
                    ->sortable(),
            ])
            ->paginated([10, 25, 50]);
    }
}
