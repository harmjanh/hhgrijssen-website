<?php

namespace App\Notifications;

use App\Models\ChurchAdministrationContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChurchAdministrationContactConfirmation extends Notification implements ShouldQueue
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
            ->subject('Bevestiging: Uw gegevens zijn ontvangen')
            ->greeting('Beste ' . $this->contact->name)
            ->line('Bedankt voor het achterlaten van uw gegevens voor de kerkelijke administratie.')
            ->line('We hebben de volgende gegevens van u ontvangen:')
            ->line('')
            ->line('Naam: ' . $this->contact->name)
            ->line('E-mailadres: ' . $this->contact->email)
            ->line('Telefoonnummer: ' . $this->contact->phone_number)
            ->line('')
            ->line('Het kerkelijk bureau zal zo spoedig mogelijk contact met u opnemen.')
            ->line('')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }
}
