<?php

namespace App\Notifications;

use App\Models\SolidarityFundAuthorization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolidarityFundAuthorizationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected SolidarityFundAuthorization $authorization
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
        $authorization = $this->authorization;

        return (new MailMessage)
            ->subject('Bevestiging Machtiging Solidariteitsfonds')
            ->greeting('Beste ' . $authorization->name)
            ->line('We hebben uw machtiging voor het Solidariteitsfonds ontvangen.')
            ->line('Hier zijn de details van uw machtiging:')
            ->line('Naam: ' . $authorization->name)
            ->line('Voorletters: ' . $authorization->initials)
            ->line('Adres: ' . $authorization->street)
            ->line('Postcode: ' . $authorization->zipcode)
            ->line('Woonplaats: ' . $authorization->city)
            ->line('Datum: ' . $authorization->submission_date->format('d-m-Y'))
            ->line('De incasso zal jaarlijks in november plaatsvinden.')
            ->line('Indien uw lidmaatschap van de kerkelijke gemeente wordt stopgezet, wordt de incasso automatisch stopgezet.')
            ->line('Met vriendelijke groet,')
            ->line('Kerkvoogdij Hersteld Hervormde Gemeente te Rijssen');
    }
}
