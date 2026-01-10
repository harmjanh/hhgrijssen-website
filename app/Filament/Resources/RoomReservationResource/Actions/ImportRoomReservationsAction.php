<?php

namespace App\Filament\Resources\RoomReservationResource\Actions;

use App\Models\Agenda;
use App\Models\AgendaItem;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportRoomReservationsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('CSV Importeren')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                FileUpload::make('file')
                    ->label('CSV Bestand')
                    ->required()
                    ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain'])
                    ->maxSize(5120) // 5MB
                    ->helperText('Upload een CSV bestand met de volgende kolommen: datum, start tijd, eind tijd, zaal, titel, e-mailadres contactpersoon, opnemen in agenda'),
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

            $handle = fopen($file, 'r');
            if ($handle === false) {
                Notification::make()
                    ->title('Fout')
                    ->body('Het bestand kon niet worden geopend.')
                    ->danger()
                    ->send();
                return;
            }

            // Read header row
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                Notification::make()
                    ->title('Fout')
                    ->body('Het CSV bestand is leeg of ongeldig.')
                    ->danger()
                    ->send();
                return;
            }

            // Normalize headers (lowercase, trim)
            $headers = array_map(function ($header) {
                return strtolower(trim($header));
            }, $headers);

            // Find column indices
            $dateIndex = array_search('datum', $headers);
            $startTimeIndex = array_search('start tijd', $headers);
            $endTimeIndex = array_search('eind tijd', $headers);
            $roomIndex = array_search('zaal', $headers);
            $titleIndex = array_search('titel', $headers);
            $emailIndex = array_search('e-mailadres contactpersoon', $headers);
            $includeInAgendaIndex = array_search('opnemen in agenda', $headers);

            // Also try alternative header names
            if ($dateIndex === false) {
                $dateIndex = array_search('date', $headers);
            }
            if ($startTimeIndex === false) {
                $startTimeIndex = array_search('starttijd', $headers) ?: array_search('start_tijd', $headers);
            }
            if ($endTimeIndex === false) {
                $endTimeIndex = array_search('eindtijd', $headers) ?: array_search('eind_tijd', $headers);
            }
            if ($roomIndex === false) {
                $roomIndex = array_search('room', $headers);
            }
            if ($titleIndex === false) {
                $titleIndex = array_search('subject', $headers) ?: array_search('onderwerp', $headers);
            }
            if ($emailIndex === false) {
                $emailIndex = array_search('email', $headers) ?: array_search('e-mail', $headers) ?: array_search('e-mailadres', $headers);
            }
            if ($includeInAgendaIndex === false) {
                $includeInAgendaIndex = array_search('opnemen_in_agenda', $headers) ?: array_search('agenda', $headers);
            }

            // Validate required columns (opnemen in agenda is optional)
            if ($dateIndex === false || $startTimeIndex === false || $endTimeIndex === false ||
                $roomIndex === false || $titleIndex === false || $emailIndex === false) {
                fclose($handle);
                Notification::make()
                    ->title('Fout')
                    ->body('Het CSV bestand mist verplichte kolommen. Vereist: datum, start tijd, eind tijd, zaal, titel, e-mailadres contactpersoon')
                    ->danger()
                    ->send();
                return;
            }

            $imported = 0;
            $errors = [];
            $rowNumber = 1;

            DB::beginTransaction();

            try {
                while (($row = fgetcsv($handle)) !== false) {
                    $rowNumber++;

                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Get values
                    $date = trim($row[$dateIndex] ?? '');
                    $startTime = trim($row[$startTimeIndex] ?? '');
                    $endTime = trim($row[$endTimeIndex] ?? '');
                    $roomName = trim($row[$roomIndex] ?? '');
                    $title = trim($row[$titleIndex] ?? '');
                    $email = trim($row[$emailIndex] ?? '');
                    $includeInAgenda = $includeInAgendaIndex !== false ? trim($row[$includeInAgendaIndex] ?? '') : '';

                    // Validate required fields
                    if (empty($date) || empty($startTime) || empty($endTime) ||
                        empty($roomName) || empty($title) || empty($email)) {
                        $errors[] = "Regel {$rowNumber}: Ontbrekende verplichte velden";
                        continue;
                    }

                    // Validate email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Regel {$rowNumber}: Ongeldig e-mailadres: {$email}";
                        continue;
                    }

                    // Find or create room
                    $room = Room::where('name', $roomName)->first();
                    if (!$room) {
                        $errors[] = "Regel {$rowNumber}: Zaal niet gevonden: {$roomName}";
                        continue;
                    }

                    // Find or create user
                    $user = User::where('email', $email)->first();
                    $isNewUser = false;

                    if (!$user) {
                        $user = User::create([
                            'name' => $email, // Use email as name initially
                            'email' => $email,
                            'password' => Hash::make(Str::random(32)), // Random password
                            'role' => 'user',
                        ]);
                        $isNewUser = true;
                    }

                    // Parse dates and times
                    // Try multiple date formats
                    $dateFormats = ['Y-m-d', 'd-m-Y', 'Y/m/d', 'd/m/Y'];
                    $timeFormats = ['H:i', 'H:i:s', 'G:i', 'G:i:s'];

                    $startDateTime = null;
                    $endDateTime = null;

                    foreach ($dateFormats as $dateFormat) {
                        foreach ($timeFormats as $timeFormat) {
                            try {
                                $startDateTime = Carbon::createFromFormat($dateFormat . ' ' . $timeFormat, $date . ' ' . $startTime);
                                $endDateTime = Carbon::createFromFormat($dateFormat . ' ' . $timeFormat, $date . ' ' . $endTime);
                                break 2; // Break out of both loops
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }

                    // If parsing failed, try Carbon::parse as fallback
                    if (!$startDateTime || !$endDateTime) {
                        try {
                            $startDateTime = Carbon::parse($date . ' ' . $startTime);
                            $endDateTime = Carbon::parse($date . ' ' . $endTime);
                        } catch (\Exception $e) {
                            $errors[] = "Regel {$rowNumber}: Ongeldige datum/tijd format (datum: {$date}, start: {$startTime}, eind: {$endTime})";
                            continue;
                        }
                    }

                    // Validate end time is after start time
                    if ($endDateTime <= $startDateTime) {
                        $errors[] = "Regel {$rowNumber}: Eindtijd moet na starttijd zijn";
                        continue;
                    }

                    // Check for time conflicts
                    if (RoomReservation::hasTimeConflict($room->id, $startDateTime, $endDateTime)) {
                        $errors[] = "Regel {$rowNumber}: Tijdconflict voor zaal '{$roomName}' op {$startDateTime->format('d-m-Y H:i')}";
                        continue;
                    }

                    // Create reservation
                    $reservation = RoomReservation::create([
                        'user_id' => $user->id,
                        'room_id' => $room->id,
                        'subject' => $title,
                        'number_of_people' => 20, // Default value
                        'start_time' => $startDateTime,
                        'end_time' => $endDateTime,
                    ]);

                    // Create agenda item if requested
                    if (strtolower(trim($includeInAgenda)) === 'ja' || strtolower(trim($includeInAgenda)) === 'yes') {
                        // Get or create the 'Agenda' agenda
                        $agenda = Agenda::firstOrCreate(
                            ['title' => 'Agenda']
                        );

                        // Generate unique UID for the agenda item
                        $uid = 'room-reservation-' . $reservation->id;

                        AgendaItem::updateOrCreate(
                            [
                                'uid' => $uid,
                                'agenda_id' => $agenda->id,
                            ],
                            [
                                'title' => $title,
                                'description' => "Zaalreservering: {$roomName}",
                                'location' => $roomName,
                                'start_date' => $startDateTime,
                                'end_date' => $endDateTime,
                            ]
                        );
                    }

                    $imported++;

                    // Send welcome email to new users
                    if ($isNewUser) {
                        $user->notify(new WelcomeUserNotification());
                    }
                }

                DB::commit();
                fclose($handle);

                $message = "{$imported} reserveringen geïmporteerd.";
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
                fclose($handle);

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
}
