<?php

namespace App\Filament\Resources\CatechesisRegistrationResource\Pages;

use App\Enums\CatechesisGroup;
use App\Filament\Resources\CatechesisRegistrationResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCatechesisRegistration extends ViewRecord
{
    protected static string $resource = CatechesisRegistrationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('season.name')->label('Seizoen'),
                TextEntry::make('first_name')->label('Voornaam'),
                TextEntry::make('last_name')->label('Achternaam'),
                TextEntry::make('email')->label('E-mailadres'),
                TextEntry::make('phone')->label('Telefoonnummer'),
                TextEntry::make('group')
                    ->label('Groep')
                    ->formatStateUsing(fn (CatechesisGroup $state): string => $state->label()),
                TextEntry::make('created_at')
                    ->label('Ingeschreven op')
                    ->dateTime('d-m-Y H:i'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
