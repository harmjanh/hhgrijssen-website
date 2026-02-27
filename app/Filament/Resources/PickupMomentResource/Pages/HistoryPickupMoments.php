<?php

namespace App\Filament\Resources\PickupMomentResource\Pages;

use App\Filament\Resources\PickupMomentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class HistoryPickupMoments extends ListRecords
{
    protected static string $resource = PickupMomentResource::class;

    protected static ?string $title = 'Historische afhaalmomenten';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upcoming')
                ->label('Toekomstige afhaalmomenten')
                ->url(PickupMomentResource::getUrl('index')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('date', '<', now()->startOfDay())
            ->orderBy('date', 'desc');
    }
}
