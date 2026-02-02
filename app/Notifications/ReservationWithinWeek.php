<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationWithinWeek extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected RoomReservation $reservation,
        protected bool $isNew = true
    ) {
        $this->reservation->load(['user', 'room']);
    }

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
        $reservation = $this->reservation;
        $user = $reservation->user;
        $room = $reservation->room;

        $subject = $this->isNew
            ? 'Nieuwe zaalreservering (binnen 7 dagen)'
            : 'Zaalreservering gewijzigd (binnen 7 dagen)';

        $intro = $this->isNew
            ? 'Er is een nieuwe zaalreservering aangemaakt met een aanvangsdatum binnen 7 dagen.'
            : 'Er is een zaalreservering gewijzigd met een aanvangsdatum binnen 7 dagen.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Beste koster,')
            ->line($intro)
            ->line('')
            ->line('**Details van de reservering:**')
            ->line('**Gebruiker:** ' . ($user ? $user->name : 'Onbekend'))
            ->line('**E-mail:** ' . ($user ? $user->email : 'Onbekend'))
            ->line('**Zaal:** ' . ($room ? $room->name : 'Onbekend'))
            ->line('**Onderwerp:** ' . $reservation->subject)
            ->line('**Aantal personen:** ' . $reservation->number_of_people)
            ->line('**Starttijd:** ' . $reservation->start_time->format('d-m-Y H:i'))
            ->line('**Eindtijd:** ' . $reservation->end_time->format('d-m-Y H:i'));
    }
}
