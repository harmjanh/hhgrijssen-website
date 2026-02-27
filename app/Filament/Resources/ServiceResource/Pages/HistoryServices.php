<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class HistoryServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected static ?string $title = 'Historische diensten';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upcoming')
                ->label('Komende diensten')
                ->url(ServiceResource::getUrl('index')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('agenda_items.start_date', '<', now())
            ->orderBy('agenda_items.start_date', 'desc');
    }
}
