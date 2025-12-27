<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected RoomReservation $reservation,
        protected string $userName
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
        $reservation = $this->reservation;
        $room = $reservation->room;
        $startTime = $reservation->start_time;
        $endTime = $reservation->end_time;

        return (new MailMessage)
            ->subject('Zaalreservering geannuleerd')
            ->greeting('Beste koster')
            ->line('Een zaalreservering is geannuleerd door ' . $this->userName . '.')
            ->line('**Details van de geannuleerde reservering:**')
            ->line('**Gebruiker:** ' . $this->userName)
            ->line('**Zaal:** ' . ($room ? $room->name : 'Onbekend'))
            ->line('**Onderwerp:** ' . $reservation->subject)
            ->line('**Aantal personen:** ' . $reservation->number_of_people)
            ->line('**Starttijd:** ' . $startTime->format('d-m-Y H:i'))
            ->line('**Eindtijd:** ' . $endTime->format('d-m-Y H:i'))
            ->line('')
            ->line('De zaal is nu weer beschikbaar voor deze tijdsperiode.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'user_name' => $this->userName,
        ];
    }
}



