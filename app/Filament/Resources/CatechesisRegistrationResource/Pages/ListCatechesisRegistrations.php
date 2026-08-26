<?php

namespace App\Filament\Resources\CatechesisRegistrationResource\Pages;

use App\Exports\CatechesisRegistrationsExport;
use App\Filament\Resources\CatechesisRegistrationResource;
use App\Models\CatechesisSeason;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListCatechesisRegistrations extends ListRecords
{
    protected static string $resource = CatechesisRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export naar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Forms\Components\Select::make('season_id')
                        ->label('Seizoen')
                        ->options(fn () => CatechesisSeason::query()->orderByDesc('name')->pluck('name', 'id'))
                        ->default(fn (): ?int => CatechesisSeason::current()?->id)
                        ->required()
                        ->helperText('Exporteer alle inschrijvingen van het gekozen seizoen.'),
                ])
                ->action(function (array $data) {
                    $season = CatechesisSeason::findOrFail($data['season_id']);

                    return Excel::download(
                        new CatechesisRegistrationsExport($season),
                        'catechisatie-inschrijvingen-'.Str::slug($season->name).'.xlsx'
                    );
                }),
        ];
    }
}
