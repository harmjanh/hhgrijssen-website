<?php

namespace App\Filament\Resources\ScipioRegistrationResource\Pages;

use App\Filament\Resources\ScipioRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScipioRegistration extends EditRecord
{
    protected static string $resource = ScipioRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
