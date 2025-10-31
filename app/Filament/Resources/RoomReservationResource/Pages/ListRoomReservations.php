<?php

namespace App\Filament\Resources\RoomReservationResource\Pages;

use App\Filament\Resources\RoomReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomReservations extends ListRecords
{
    protected static string $resource = RoomReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
