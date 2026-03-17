<?php

namespace App\Notifications;

use App\Models\RoomReservation;
use App\Notifications\Concerns\FormatsRoomReservationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ReservationReminder extends Notification implements ShouldQueue
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
        // Create a signed URL for cancellation (valid for 7 days)
        $cancelUrl = URL::signedRoute(
            'room-reservations.cancel',
            ['roomReservation' => $this->reservation->id],
            now()->addDays(7)
        );

        return $this->addReservationDetails(
            (new MailMessage)
            ->subject('Herinnering: Zaalreservering over 2 dagen')
            ->greeting('Beste ' . $notifiable->name)
            ->line('Dit is een herinnering dat u een zaalreservering heeft over 2 dagen.')
            ->line(''),
            $this->reservation,
            false
        )
            ->line('Als u deze reservering wilt annuleren, klik dan op de onderstaande knop.')
            ->action('Reservering annuleren', $cancelUrl)
            ->line('Deze link is 7 dagen geldig.')
            ->line('Bedankt!');
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
            'start_time' => $this->reservation->start_time->toIso8601String(),
        ];
    }
}

