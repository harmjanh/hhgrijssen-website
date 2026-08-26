<?php

namespace App\Filament\Resources\CatechesisRegistrationResource\Pages;

use App\Filament\Resources\CatechesisRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCatechesisRegistration extends EditRecord
{
    protected static string $resource = CatechesisRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
