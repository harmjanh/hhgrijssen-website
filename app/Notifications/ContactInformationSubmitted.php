<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactInformationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $user,
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
            ->subject('Nieuwe contactgegevens ontvangen - ' . $this->data['name'])
            ->greeting('Beste kerkelijk bureau,')
            ->line('Er zijn nieuwe contactgegevens ontvangen van een gebruiker die niet voorkomt in de Scipio registraties.')
            ->line('')
            ->line('Gebruiker gegevens:')
            ->line('Naam: ' . $this->data['name'])
            ->line('E-mail: ' . $this->data['email'])
            ->line('Telefoonnummer: ' . $this->data['phonenumber'])
            ->when(!empty($this->data['street']) || !empty($this->data['number']), fn ($mail) => $mail->line('Straat en huisnummer: ' . trim(($this->data['street'] ?? '') . ' ' . ($this->data['number'] ?? ''))))
            ->when(!empty($this->data['city']), fn ($mail) => $mail->line('Plaats: ' . $this->data['city']))
            ->line('')
            ->line('De gebruiker heeft aangegeven dat deze gegevens ontbreken in de Scipio registraties.')
            ->line('Gelieve deze gegevens te verwerken in het systeem.')
            ->line('')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }
}
