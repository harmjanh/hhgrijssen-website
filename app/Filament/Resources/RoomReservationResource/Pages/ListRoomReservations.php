<?php

namespace App\Filament\Resources\RoomReservationResource\Pages;

use App\Filament\Resources\RoomReservationResource;
use App\Filament\Resources\RoomReservationResource\Actions\ExportExampleCsvAction;
use App\Filament\Resources\RoomReservationResource\Actions\ImportRoomReservationsAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomReservations extends ListRecords
{
    protected static string $resource = RoomReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportExampleCsvAction::make(),
            ImportRoomReservationsAction::make(),
        ];
    }
}
