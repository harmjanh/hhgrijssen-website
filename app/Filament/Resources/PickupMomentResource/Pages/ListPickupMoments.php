<?php

namespace App\Filament\Resources\PickupMomentResource\Pages;

use App\Filament\Resources\PickupMomentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPickupMoments extends ListRecords
{
    protected static string $resource = PickupMomentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
