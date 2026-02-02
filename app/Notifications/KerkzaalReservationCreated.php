<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KerkzaalReservationCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected RoomReservation $reservation
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

        return (new MailMessage)
            ->subject('Nieuwe reservering kerkzaal')
            ->greeting('Beste,')
            ->line('Er is een nieuwe reservering voor de kerkzaal aangemaakt.')
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
