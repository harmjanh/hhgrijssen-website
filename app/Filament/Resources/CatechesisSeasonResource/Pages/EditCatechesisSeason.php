<?php

namespace App\Filament\Resources\CatechesisSeasonResource\Pages;

use App\Filament\Resources\CatechesisSeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCatechesisSeason extends EditRecord
{
    protected static string $resource = CatechesisSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
