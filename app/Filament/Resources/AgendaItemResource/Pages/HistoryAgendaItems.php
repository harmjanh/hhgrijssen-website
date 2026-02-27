<?php

namespace App\Filament\Resources\AgendaItemResource\Pages;

use App\Filament\Resources\AgendaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class HistoryAgendaItems extends ListRecords
{
    protected static string $resource = AgendaItemResource::class;

    protected static ?string $title = 'Historische agenda-items';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upcoming')
                ->label('Toekomstige agenda-items')
                ->url(AgendaItemResource::getUrl('index')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('start_date', '<', now())
            ->orderBy('start_date', 'desc');
    }
}
