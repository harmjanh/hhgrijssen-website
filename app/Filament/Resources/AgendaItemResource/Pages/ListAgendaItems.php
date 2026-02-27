<?php

namespace App\Filament\Resources\AgendaItemResource\Pages;

use App\Filament\Resources\AgendaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAgendaItems extends ListRecords
{
    protected static string $resource = AgendaItemResource::class;

    protected static ?string $title = 'Toekomstige agenda-items';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nieuw agenda-item'),
            Actions\Action::make('history')
                ->label('Historie')
                ->url(AgendaItemResource::getUrl('history')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('start_date', '>=', now())
            ->reorder('start_date', 'asc');
    }
}
