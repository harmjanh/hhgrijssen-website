<?php

namespace App\Notifications;

use App\Models\PrivacyConsent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrivacyConsentSubmitted extends Notification implements ShouldQueue
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
        $user = $consent->user;

        return (new MailMessage)
            ->subject('Nieuw Privacy Toestemmingsformulier - ' . ($user ? $user->name : 'Onbekend') . ' - ' . $consent->created_at->format('d-m-Y'))
            ->greeting('Ha Arjan,')
            ->line('Er is een nieuw privacy toestemmingsformulier ingediend.')
            ->line('Details:')
            ->line('Ingediend door: ' . ($user ? $user->name : 'Onbekend'))
            ->line('Datum indiening: ' . $consent->created_at->format('d-m-Y H:i'))
            ->line('')
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
            ->line('Datum: ' . $consent->submission_date->format('d-m-Y'));
    }
}
