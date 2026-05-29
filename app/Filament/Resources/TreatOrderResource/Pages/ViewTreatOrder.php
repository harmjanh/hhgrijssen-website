<?php

namespace App\Filament\Resources\TreatOrderResource\Pages;

use App\Filament\Resources\TreatOrderResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTreatOrder extends ViewRecord
{
    protected static string $resource = TreatOrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')->label('Naam'),
                TextEntry::make('email')->label('E-mail'),
                TextEntry::make('phone')->label('Telefoon'),
                TextEntry::make('snoeprollen_quantity')
                    ->label('Snoeprollen')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "{$state} bestelling(en) à 10 stuks" : '—'),
                TextEntry::make('stroopwafels_quantity')
                    ->label('Stroopwafels')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "{$state} bestelling(en) à 3 pakjes" : '—'),
                TextEntry::make('total_amount')->label('Totaalbedrag')->money('EUR'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Betaald',
                        'pending' => 'In behandeling',
                        'failed' => 'Mislukt',
                        default => $state,
                    }),
                TextEntry::make('payment_id')->label('Mollie betaling')->placeholder('—'),
                TextEntry::make('remarks')->label('Opmerkingen')->columnSpanFull()->placeholder('—'),
                TextEntry::make('created_at')->label('Besteld op')->dateTime('d-m-Y H:i'),
            ]);
    }
}
