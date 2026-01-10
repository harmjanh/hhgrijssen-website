<?php

namespace App\Filament\Resources\RoomReservationResource\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;

class ExportExampleCsvAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'export-example';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Download Voorbeeld CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                return $this->downloadExampleCsv();
            });
    }

    protected function downloadExampleCsv()
    {
        $csv = "datum,start tijd,eind tijd,zaal,titel,e-mailadres contactpersoon,opnemen in agenda\n";
        $csv .= "2026-01-15,19:00,21:00,Zaal achter kansel,Jeugdvereniging activiteit,jeugdvereniging@hhgrijssen.nl,ja\n";
        $csv .= "2026-01-16,18:30,20:30,Consistorie,Instroomcatechese,catechese@hhgrijssen.nl,nee\n";
        $csv .= "2026-01-17,19:30,21:30,Zaal onder gallerij,Overleg vergadering,contact@hhgrijssen.nl,ja\n";

        return Response::streamDownload(function () use ($csv) {
            echo $csv;
        }, 'voorbeeld_zaalreserveringen.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="voorbeeld_zaalreserveringen.csv"',
        ]);
    }
}
