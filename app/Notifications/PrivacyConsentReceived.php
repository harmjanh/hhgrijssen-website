<?php

namespace App\Notifications;

use App\Models\PrivacyConsent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrivacyConsentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected PrivacyConsent $consent
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
        $consent = $this->consent;

        $message = (new MailMessage)
            ->subject('Bevestiging Privacy Toestemmingsformulier')
            ->greeting('Beste ' . $consent->name)
            ->line('We hebben uw privacy toestemmingsformulier ontvangen.')
            ->line('Hier zijn de details van uw toestemmingen:')
            ->line('Naam: ' . $consent->name)
            ->line('Adres: ' . $consent->street)
            ->line('Postcode: ' . $consent->zipcode)
            ->line('Woonplaats: ' . $consent->city)
            ->line('Geboortedatum: ' . $consent->birth_date->format('d-m-Y'))
            ->line('')
            ->line('Toestemmingen:')
            ->line('Voorbede/dankzegging tijdens eredienst: ' . ($consent->voorbede_eredienst ? 'Wel toestemming' : 'Geen toestemming'))
            ->line('Voorbede/dankzegging in De Zaaier: ' . ($consent->voorbede_zaaier ? 'Wel toestemming' : 'Geen toestemming'))
            ->line('Verjaardag 75+ in De Zaaier: ' . ($consent->verjaardag_zaaier ? 'Wel toestemming' : 'Geen toestemming'))
            ->line('Gegevens aan RSV: ' . ($consent->rsv_gegevens ? 'Wel toestemming' : 'Geen toestemming'))
            ->line('')
            ->line('Plaats: ' . $consent->place)
            ->line('Datum: ' . $consent->submission_date->format('d-m-Y'))
            ->line('')
            ->line('U kunt op ieder moment een weergave van uw gegevens opvragen bij het kerkelijk bureau.')
            ->line('Onjuistheden en wijzigingen kunt u doorgeven aan het kerkelijk bureau.')
            ->line('Met vriendelijke groet,')
            ->line('Kerkvoogdij Hersteld Hervormde Gemeente te Rijssen');

        return $message;
    }
}
