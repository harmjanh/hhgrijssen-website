<?php

namespace App\Notifications;

use App\Models\ChurchAdministrationContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChurchAdministrationContactReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected ChurchAdministrationContact $contact
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
            ->subject('Nieuw contact voor kerkelijke administratie')
            ->greeting('Beste kerkelijk bureau')
            ->line('Er is een nieuw contactformulier ingevuld voor de kerkelijke administratie.')
            ->line('')
            ->line('Gegevens:')
            ->line('Naam: ' . $this->contact->name)
            ->line('E-mailadres: ' . $this->contact->email)
            ->line('Telefoonnummer: ' . $this->contact->phone_number)
            ->line('')
            ->line('Verzonden op: ' . $this->contact->created_at->format('d-m-Y H:i'))
            ->line('')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen Website');
    }
}
