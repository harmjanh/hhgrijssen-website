<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use App\Notifications\Concerns\FormatsRoomReservationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancelled extends Notification implements ShouldQueue
{
    use Queueable;
    use FormatsRoomReservationMail;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected RoomReservation $reservation
    ) {
        $this->reservation->loadMissing(['user', 'room']);
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
        $userName = $this->reservation->user?->name ?? 'Onbekend';

        return $this->addReservationDetails(
            (new MailMessage)
            ->subject('Zaalreservering geannuleerd')
            ->greeting('Beste koster,')
            ->line('Een zaalreservering is geannuleerd door ' . $userName . '.')
            ->line(''),
            $this->reservation
        )
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
            'user_name' => $this->reservation->user?->name,
            'room_name' => $this->reservation->room?->name,
            'subject' => $this->reservation->subject,
            'number_of_people' => $this->reservation->number_of_people,
            'start_time' => $this->reservation->start_time?->toIso8601String(),
            'end_time' => $this->reservation->end_time?->toIso8601String(),
        ];
    }
}




