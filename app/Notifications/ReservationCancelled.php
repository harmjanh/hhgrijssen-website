<?php

namespace App\Notifications;

use Carbon\Carbon;
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
        protected string $userName,
        protected string $roomName,
        protected string $subject,
        protected int $numberOfPeople,
        protected string $startTime,
        protected string $endTime
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
        $startTime = Carbon::parse($this->startTime);
        $endTime = Carbon::parse($this->endTime);

        return (new MailMessage)
            ->subject('Zaalreservering geannuleerd')
            ->greeting('Beste koster,')
            ->line('Een zaalreservering is geannuleerd door ' . $this->userName . '.')
            ->line('**Details van de geannuleerde reservering:**')
            ->line('**Gebruiker:** ' . $this->userName)
            ->line('**Zaal:** ' . $this->roomName)
            ->line('**Onderwerp:** ' . $this->subject)
            ->line('**Aantal personen:** ' . $this->numberOfPeople)
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
            'user_name' => $this->userName,
            'room_name' => $this->roomName,
            'subject' => $this->subject,
            'number_of_people' => $this->numberOfPeople,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];
    }
}




