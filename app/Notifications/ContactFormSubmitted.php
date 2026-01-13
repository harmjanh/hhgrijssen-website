<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactFormSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected array $data
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
        return (new MailMessage)
            ->subject('Nieuw contactformulier bericht: ' . $this->data['subject'])
            ->greeting('Beste webmaster')
            ->replyTo($this->data['email'])
            ->line('Er is een nieuw bericht via het contactformulier ontvangen.')
            ->line('')
            ->line('Naam: ' . $this->data['name'])
            ->line('E-mailadres: ' . $this->data['email'])
            ->line('Onderwerp: ' . $this->data['subject'])
            ->line('')
            ->line('Bericht:')
            ->line($this->data['message'])
            ->line('')
            ->line('Verzonden op: ' . now()->format('d-m-Y H:i'));
    }
}
