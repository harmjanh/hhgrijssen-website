<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use App\Notifications\Concerns\FormatsRoomReservationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationWithinWeek extends Notification implements ShouldQueue
{
    use Queueable;
    use FormatsRoomReservationMail;

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
        $subject = $this->isNew
            ? 'Nieuwe zaalreservering (binnen 7 dagen)'
            : 'Zaalreservering gewijzigd (binnen 7 dagen)';

        $intro = $this->isNew
            ? 'Er is een nieuwe zaalreservering aangemaakt met een aanvangsdatum binnen 7 dagen.'
            : 'Er is een zaalreservering gewijzigd met een aanvangsdatum binnen 7 dagen.';

        return $this->addReservationDetails(
            (new MailMessage)
            ->subject($subject)
            ->greeting('Beste koster,')
            ->line($intro)
            ->line(''),
            $this->reservation
        );
    }
}
