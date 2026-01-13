<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class WeeklyReservationsOverview extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Collection $reservations,
        protected Carbon $startOfWeek,
        protected Carbon $endOfWeek
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $periode = $this->startOfWeek->format('d-m-Y') . ' t/m ' . $this->endOfWeek->format('d-m-Y');
        $mailMessage = (new MailMessage)
            ->subject('Wekelijks overzicht zaalreserveringen - Periode: ' . $periode)
            ->greeting('Beste koster,')
            ->line('Hierbij het overzicht van alle zaalreserveringen voor de komende week.')
            ->line('**Periode:** ' . $periode)
            ->line('');

        if ($this->reservations->isEmpty()) {
            $mailMessage->line('Er zijn geen reserveringen voor de komende week.');
        } else {
            $mailMessage->line('**Aantal reserveringen:** ' . $this->reservations->count())
                ->line('')
                ->line('**Reserveringen:**');

            // Group reservations by date
            $reservationsByDate = $this->reservations->groupBy(function ($reservation) {
                return $reservation->start_time ? $reservation->start_time->format('Y-m-d') : 'unknown';
            });

            foreach ($reservationsByDate as $date => $dayReservations) {
                if ($date === 'unknown') {
                    continue; // Skip reservations without start_time
                }

                $dateFormatted = Carbon::parse($date)->format('l d-m-Y');
                $mailMessage->line('')
                    ->line('**' . $dateFormatted . '**');

                foreach ($dayReservations as $reservation) {
                    $user = $reservation->user;
                    $room = $reservation->room;
                    $userName = $user ? $user->name : 'Onbekend';
                    $roomName = $room ? $room->name : 'Onbekend';

                    // Check if start_time and end_time are not null
                    $startTime = $reservation->start_time ? $reservation->start_time->format('H:i') : 'Onbekend';
                    $endTime = $reservation->end_time ? $reservation->end_time->format('H:i') : 'Onbekend';

                    $mailMessage->line('• **' . $startTime . ' - ' . $endTime . '**')
                        ->line('  Zaal: ' . $roomName)
                        ->line('  Gebruiker: ' . $userName)
                        ->line('  Onderwerp: ' . ($reservation->subject ?? 'Geen onderwerp'))
                        ->line('  Aantal personen: ' . ($reservation->number_of_people ?? 'Onbekend'))
                        ->line('--------------------------------');
                }
            }
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservations_count' => $this->reservations->count(),
            'start_of_week' => $this->startOfWeek->toIso8601String(),
            'end_of_week' => $this->endOfWeek->toIso8601String(),
        ];
    }
}

