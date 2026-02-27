<?php

namespace App\Filament\Resources\AgendaItemResource\Pages;

use App\Filament\Resources\AgendaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAgendaItem extends ViewRecord
{
    protected static string $resource = AgendaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Bewerken'),
        ];
    }
}
