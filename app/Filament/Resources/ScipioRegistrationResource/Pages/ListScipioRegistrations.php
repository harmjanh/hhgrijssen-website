<?php

namespace App\Filament\Resources\ScipioRegistrationResource\Pages;

use App\Filament\Resources\ScipioRegistrationResource;
use App\Filament\Resources\ScipioRegistrationResource\Actions\ImportScipioRegistrationsAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScipioRegistrations extends ListRecords
{
    protected static string $resource = ScipioRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportScipioRegistrationsAction::make(),
        ];
    }
}
