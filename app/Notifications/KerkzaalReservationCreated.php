<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use App\Notifications\Concerns\FormatsRoomReservationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KerkzaalReservationCreated extends Notification implements ShouldQueue
{
    use Queueable;
    use FormatsRoomReservationMail;

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
        return $this->addReservationDetails(
            (new MailMessage)
            ->subject('Nieuwe reservering kerkzaal')
            ->greeting('Beste,')
            ->line('Er is een nieuwe reservering voor de kerkzaal aangemaakt.')
            ->line(''),
            $this->reservation
        );
    }
}
