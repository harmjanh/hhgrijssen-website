<?php

namespace App\Exports;

use App\Models\CatechesisRegistration;
use App\Models\CatechesisSeason;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CatechesisRegistrationsExport implements FromQuery, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(public CatechesisSeason $season) {}

    public function query(): Builder
    {
        return CatechesisRegistration::query()
            ->where('season_id', $this->season->id)
            ->orderBy('group')
            ->orderBy('last_name')
            ->orderBy('first_name');
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Voornaam',
            'Achternaam',
            'E-mailadres',
            'Telefoonnummer',
            'Groep',
            'Ingeschreven op',
        ];
    }

    /**
     * @param  CatechesisRegistration  $registration
     * @return list<string>
     */
    public function map($registration): array
    {
        return [
            $registration->first_name,
            $registration->last_name,
            $registration->email,
            $registration->phone,
            $registration->group->label(),
            $registration->created_at->format('d-m-Y H:i'),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 22,
            'C' => 32,
            'D' => 18,
            'E' => 48,
            'F' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Catechisatie '.$this->season->name;
    }
}
