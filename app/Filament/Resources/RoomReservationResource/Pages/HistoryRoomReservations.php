<?php

namespace App\Filament\Resources\RoomReservationResource\Pages;

use App\Filament\Resources\RoomReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class HistoryRoomReservations extends ListRecords
{
    protected static string $resource = RoomReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upcoming')
                ->label('Komende Reserveringen')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('start_time', '<', now())
            ->orderBy('start_time', 'desc');
    }
}
