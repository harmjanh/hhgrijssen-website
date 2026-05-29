<?php

namespace App\Exports;

use App\Models\TreatOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TreatOrdersExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    public function collection()
    {
        return TreatOrder::query()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Naam',
            'E-mail',
            'Telefoon',
            'Snoeprollen (×10)',
            'Stroopwafels (×3 pakjes)',
            'Totaalbedrag',
            'Status',
            'Mollie betaling',
            'Opmerkingen',
            'Besteld op',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->name,
            $order->email,
            $order->phone,
            $order->snoeprollen_quantity,
            $order->stroopwafels_quantity,
            number_format($order->total_amount, 2, ',', '.'),
            match ($order->status) {
                'paid' => 'Betaald',
                'pending' => 'In behandeling',
                'failed' => 'Mislukt',
                default => $order->status,
            },
            $order->payment_id ?? '',
            $order->remarks ?? '',
            $order->created_at->format('d-m-Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 30,
            'D' => 18,
            'E' => 18,
            'F' => 22,
            'G' => 14,
            'H' => 16,
            'I' => 28,
            'J' => 35,
            'K' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
