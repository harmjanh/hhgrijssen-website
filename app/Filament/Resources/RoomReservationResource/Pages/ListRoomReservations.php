<?php

namespace App\Filament\Resources\RoomReservationResource\Pages;

use App\Filament\Resources\RoomReservationResource;
use App\Filament\Resources\RoomReservationResource\Actions\ExportExampleCsvAction;
use App\Filament\Resources\RoomReservationResource\Actions\ImportRoomReservationsAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRoomReservations extends ListRecords
{
    protected static string $resource = RoomReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('history')
                ->label('Historische Reserveringen')
                ->url(static::getResource()::getUrl('history')),
            ExportExampleCsvAction::make(),
            ImportRoomReservationsAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc');
    }
}
