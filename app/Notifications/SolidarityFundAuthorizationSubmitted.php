<?php

namespace App\Notifications;

use App\Models\SolidarityFundAuthorization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolidarityFundAuthorizationSubmitted extends Notification implements ShouldQueue
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
        $user = $authorization->user;

        return (new MailMessage)
            ->subject('Nieuwe Machtiging Solidariteitsfonds - ' . ($user ? $user->name : 'Onbekend') . ' - ' . $authorization->created_at->format('d-m-Y'))
            ->greeting('Ha Arjan,')
            ->line('Er is een nieuwe machtiging voor het Solidariteitsfonds ingediend.')
            ->line('Details:')
            ->line('Ingediend door: ' . ($user ? $user->name : 'Onbekend'))
            ->line('Datum indiening: ' . $authorization->created_at->format('d-m-Y H:i'))
            ->line('')
            ->line('Naam: ' . $authorization->name)
            ->line('Voorletters: ' . $authorization->initials)
            ->line('Adres: ' . $authorization->street)
            ->line('Postcode: ' . $authorization->zipcode)
            ->line('Woonplaats: ' . $authorization->city)
            ->line('IBAN: ' . $authorization->iban)
            ->line('Datum: ' . $authorization->submission_date->format('d-m-Y'));
    }
}
