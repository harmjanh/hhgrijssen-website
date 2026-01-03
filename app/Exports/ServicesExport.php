<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $dateFrom;
    protected $dateUntil;

    public function __construct($dateFrom, $dateUntil)
    {
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function collection()
    {
        $dateFrom = $this->dateFrom;
        $dateUntil = $this->dateUntil;

        $query = Service::with('agendaItem')
            ->whereHas('agendaItem', function ($q) use ($dateFrom, $dateUntil) {
                if ($dateFrom && $dateUntil) {
                    // Services that overlap with the date range
                    $q->whereDate('start_date', '<=', $dateUntil)
                      ->whereDate('end_date', '>=', $dateFrom);
                } elseif ($dateFrom) {
                    // Services that start or end on/after this date
                    $q->where(function ($subQuery) use ($dateFrom) {
                        $subQuery->whereDate('start_date', '>=', $dateFrom)
                                 ->orWhereDate('end_date', '>=', $dateFrom);
                    });
                } elseif ($dateUntil) {
                    // Services that start or end on/before this date
                    $q->where(function ($subQuery) use ($dateUntil) {
                        $subQuery->whereDate('start_date', '<=', $dateUntil)
                                 ->orWhereDate('end_date', '<=', $dateUntil);
                    });
                }
            })
            ->join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->select('services.*')
            ->orderBy('agenda_items.start_date', 'asc')
            ->get();

        return $query;
    }

    public function headings(): array
    {
        return [
            'Datum',
            'Aanvangstijd',
        ];
    }

    public function map($service): array
    {
        $startDate = $service->agendaItem?->start_date;

        return [
            $startDate ? $startDate->format('d-m-Y') : '',
            $startDate ? $startDate->format('H:i') : '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Datum
            'B' => 15, // Aanvangstijd
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row
        ];
    }
}
