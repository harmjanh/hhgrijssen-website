<?php

namespace App\Exports;

use App\Models\CoinOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoinOrdersExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $pickupMomentId;

    public function __construct($pickupMomentId)
    {
        $this->pickupMomentId = $pickupMomentId;
    }

    public function collection()
    {
        return CoinOrder::with('user')
            ->where('pickup_moment_id', $this->pickupMomentId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Gebruiker',
            'Naam',
            'E-mail',
            'Aantal zilveren munten',
            'Aantal gouden munten',
            'Status',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->user?->name ?? '',
            $order->name,
            $order->email,
            $order->silver_coins,
            $order->gold_coins,
            match ($order->status) {
                'paid' => 'Betaald',
                'pending' => 'In behandeling',
                'failed' => 'Mislukt',
                default => $order->status,
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID
            'B' => 20, // Gebruiker
            'C' => 25, // Naam
            'D' => 30, // E-mail
            'E' => 20, // Aantal zilveren munten
            'F' => 20, // Aantal gouden munten
            'G' => 20, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row
        ];
    }
}
