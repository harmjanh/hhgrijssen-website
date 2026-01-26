<?php

namespace App\Filament\Resources\ScipioRegistrationResource\Actions;

use App\Models\ScipioRegistration;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportScipioRegistrationsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Excel Importeren')
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
                    ->helperText('Upload een Excel bestand (.xlsx of .xls) met de volgende kolommen: Regnr. (verplicht), e-mail (optioneel), Telefoon, Mobiel'),
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
            $emailIndex = $this->findColumnIndex($headers, ['e-mail', 'email', 'E-mail', 'Email', 'E-MAIL', 'EMAIL']);
            $phoneIndex = $this->findColumnIndex($headers, ['Telefoon', 'telefoon', 'TELEFOON', 'Phone', 'phone']);
            $mobileIndex = $this->findColumnIndex($headers, ['Mobiel', 'mobiel', 'MOBIEL', 'Mobile', 'mobile', 'Mobiele']);

            // Validate required columns (only regnr is required)
            if ($regnrIndex === false) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het Excel bestand mist de verplichte kolom: Regnr.')
                    ->danger()
                    ->send();
                return;
            }

            $imported = 0;
            $updated = 0;
            $errors = [];
            $rowNumber = 1;
            $importTimestamp = now();

            // Store all rows first, then process (to handle duplicates correctly)
            $rowsToProcess = [];

            foreach ($rows as $row) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row, fn($cell) => $cell !== null && $cell !== ''))) {
                    continue;
                }

                $regnr = trim((string) ($row[$regnrIndex] ?? ''));
                $email = $emailIndex !== false ? trim((string) ($row[$emailIndex] ?? '')) : '';
                $phone = $phoneIndex !== false ? trim((string) ($row[$phoneIndex] ?? '')) : '';
                $mobile = $mobileIndex !== false ? trim((string) ($row[$mobileIndex] ?? '')) : '';

                // Validate required fields (only regnr is required)
                if (empty($regnr)) {
                    $errors[] = "Regel {$rowNumber}: Ontbrekend verplicht veld: Regnr";
                    continue;
                }

                // Validate email only if provided
                if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Regel {$rowNumber}: Ongeldig e-mailadres: {$email}";
                    continue;
                }

                // Store row data (will be processed later to handle duplicates)
                $rowsToProcess[] = [
                    'regnr' => $regnr,
                    'email' => !empty($email) ? $email : null,
                    'phonenumber' => !empty($phone) ? $phone : null,
                    'mobile' => !empty($mobile) ? $mobile : null,
                    'imported_at' => $importTimestamp,
                ];
            }

            // Process rows - if duplicate regnr exists, use the last one (newest)
            // Group by regnr and take the last occurrence
            $uniqueRows = [];
            foreach ($rowsToProcess as $row) {
                $uniqueRows[$row['regnr']] = $row;
            }

            DB::beginTransaction();

            try {
                foreach ($uniqueRows as $row) {
                    // Update or create based on regnr (the key)
                    $existing = ScipioRegistration::where('regnr', $row['regnr'])->first();

                    if ($existing) {
                        // Update existing record with new values
                        $updateData = [
                            'phonenumber' => $row['phonenumber'],
                            'mobile' => $row['mobile'],
                            'imported_at' => $row['imported_at'],
                        ];
                        
                        // Only update email if provided (can be null)
                        if (isset($row['email'])) {
                            $updateData['email'] = $row['email'];
                        }
                        
                        $existing->update($updateData);
                        $updated++;
                    } else {
                        // Create new record
                        ScipioRegistration::create($row);
                        $imported++;
                    }
                }

                DB::commit();

                $message = "{$imported} nieuwe registraties geïmporteerd";
                if ($updated > 0) {
                    $message .= ", {$updated} registraties bijgewerkt";
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
