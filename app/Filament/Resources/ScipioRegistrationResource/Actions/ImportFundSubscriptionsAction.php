<?php

namespace App\Filament\Resources\ScipioRegistrationResource\Actions;

use App\Models\ScipioRegistration;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportFundSubscriptionsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import-fund-subscriptions';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Fondsen Abonnementen Importeren')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                FileUpload::make('file')
                    ->label('Excel Bestand')
                    ->required()
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/vnd.ms-excel', // .xls
                    ])
                    ->maxSize(10240) // 10MB
                    ->helperText('Upload een Excel bestand (.xlsx of .xls) met de volgende kolommen: Regnr., Fondsnaam, Toezegging, Betaalwijze'),
            ])
            ->action(function (array $data) {
                $this->handleImport($data['file']);
            });
    }

    protected function handleImport(string $filePath): void
    {
        try {
            // FileUpload stores files in storage/app/public
            $file = Storage::disk('public')->path($filePath);

            if (!file_exists($file)) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het bestand kon niet worden gevonden.')
                    ->danger()
                    ->send();
                return;
            }

            // Read Excel file
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows)) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het Excel bestand is leeg.')
                    ->danger()
                    ->send();
                return;
            }

            // Get header row (first row)
            $headers = array_map(function ($header) {
                return trim((string) $header);
            }, array_shift($rows));

            // Find column indices - try multiple variations
            $regnrIndex = $this->findColumnIndex($headers, ['Regnr.', 'Regnr', 'regnr', 'REGNR', 'Registratienummer']);
            $fondsnaamIndex = $this->findColumnIndex($headers, ['Fondsnaam', 'fondsnaam', 'FONDSNAAM', 'Fonds naam', 'Fonds Naam']);
            $toezeggingIndex = $this->findColumnIndex($headers, ['Toezegging', 'toezegging', 'TOEZEGGING', 'Toe zegging', 'Toe Zegging']);
            $betaalwijzeIndex = $this->findColumnIndex($headers, ['Betaalwijze', 'betaalwijze', 'BETAALWIJZE', 'Betaal wijze', 'Betaal Wijze']);

            // Validate required columns
            if ($regnrIndex === false) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het Excel bestand mist de verplichte kolom: Regnr.')
                    ->danger()
                    ->send();
                return;
            }

            if ($fondsnaamIndex === false) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het Excel bestand mist de verplichte kolom: Fondsnaam.')
                    ->danger()
                    ->send();
                return;
            }

            if ($toezeggingIndex === false) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het Excel bestand mist de verplichte kolom: Toezegging.')
                    ->danger()
                    ->send();
                return;
            }

            if ($betaalwijzeIndex === false) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het Excel bestand mist de verplichte kolom: Betaalwijze.')
                    ->danger()
                    ->send();
                return;
            }

            $updated = 0;
            $skipped = 0;
            $notFound = 0;
            $errors = [];
            $rowNumber = 1;

            DB::beginTransaction();

            try {
                foreach ($rows as $row) {
                    $rowNumber++;

                    // Skip empty rows
                    if (empty(array_filter($row, fn($cell) => $cell !== null && $cell !== ''))) {
                        continue;
                    }

                    $regnr = trim((string) ($row[$regnrIndex] ?? ''));
                    $fondsnaam = trim((string) ($row[$fondsnaamIndex] ?? ''));
                    $toezegging = $row[$toezeggingIndex] ?? '';
                    $betaalwijze = trim((string) ($row[$betaalwijzeIndex] ?? ''));

                    // Validate required fields
                    if (empty($regnr)) {
                        $errors[] = "Regel {$rowNumber}: Ontbrekend verplicht veld: Regnr";
                        continue;
                    }

                    // Find the ScipioRegistration by regnr
                    $registration = ScipioRegistration::where('regnr', $regnr)->first();

                    if (!$registration) {
                        $notFound++;
                        continue;
                    }

                    // Convert toezegging to numeric value
                    $toezeggingValue = is_numeric($toezegging) ? (float) $toezegging : 0;

                    // Check conditions and update flags
                    $shouldUpdate = false;
                    $updateData = [];

                    // Check for Zaaier subscription
                    if (
                        strcasecmp($fondsnaam, 'Abonnement De Zaaier') === 0 &&
                        strcasecmp($betaalwijze, 'Incasso') === 0 &&
                        $toezeggingValue > 0
                    ) {
                        // Only set to true if it's currently false (don't change from true to false)
                        if (!$registration->has_zaaier) {
                            $updateData['has_zaaier'] = true;
                            $shouldUpdate = true;
                        }
                    }

                    // Check for Solidarity Fund subscription
                    if (
                        strcasecmp($fondsnaam, 'Solidariteitsfonds') === 0 &&
                        strcasecmp($betaalwijze, 'Incasso') === 0 &&
                        $toezeggingValue > 0
                    ) {
                        // Only set to true if it's currently false (don't change from true to false)
                        if (!$registration->has_solidarity_fund) {
                            $updateData['has_solidarity_fund'] = true;
                            $shouldUpdate = true;
                        }
                    }

                    if ($shouldUpdate) {
                        $registration->update($updateData);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }

                DB::commit();

                $message = "{$updated} registraties bijgewerkt";
                if ($skipped > 0) {
                    $message .= ", {$skipped} overgeslagen (al ingesteld of niet van toepassing)";
                }
                if ($notFound > 0) {
                    $message .= ", {$notFound} niet gevonden in database";
                }
                $message .= ".";

                if (!empty($errors)) {
                    $message .= " " . count($errors) . " fout(en) opgetreden.";
                }

                Notification::make()
                    ->title('Import voltooid')
                    ->body($message)
                    ->success()
                    ->send();

                if (!empty($errors)) {
                    Notification::make()
                        ->title('Fouten tijdens import')
                        ->body(implode("\n", array_slice($errors, 0, 10)) . (count($errors) > 10 ? "\n..." : ''))
                        ->warning()
                        ->persistent()
                        ->send();
                }

            } catch (\Exception $e) {
                DB::rollBack();

                Notification::make()
                    ->title('Fout tijdens import')
                    ->body('Er is een fout opgetreden: ' . $e->getMessage())
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Fout')
                ->body('Er is een onverwachte fout opgetreden: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Find column index by trying multiple possible header names
     */
    protected function findColumnIndex(array $headers, array $possibleNames): int|false
    {
        foreach ($possibleNames as $name) {
            $index = array_search($name, $headers, true);
            if ($index !== false) {
                return $index;
            }
        }
        return false;
    }
}
