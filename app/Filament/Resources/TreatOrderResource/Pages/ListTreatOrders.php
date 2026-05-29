<?php

namespace App\Filament\Resources\TreatOrderResource\Pages;

use App\Exports\TreatOrdersExport;
use App\Filament\Resources\TreatOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTreatOrders extends ListRecords
{
    protected static string $resource = TreatOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export naar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => Excel::download(
                    new TreatOrdersExport,
                    'traktatiebestellingen_' . now()->format('Y-m-d') . '.xlsx'
                )),
        ];
    }
}
