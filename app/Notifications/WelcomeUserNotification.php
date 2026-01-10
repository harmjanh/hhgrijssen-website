<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class WelcomeUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $token = Password::createToken($notifiable);
        $url = route('password.reset', [
            'token' => $token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Welkom bij HHG Rijssen')
            ->greeting('Beste ' . ($notifiable->name ?? 'gebruiker'))
            ->line('Welkom bij HHG Rijssen! Er is een account voor u aangemaakt.')
            ->line('U kunt nu uw wachtwoord instellen door op onderstaande link te klikken.')
            ->action('Wachtwoord Instellen', $url)
            ->line('Deze link verloopt over 60 minuten.')
            ->line('U kunt ook altijd een nieuwe link aanvragen via de website met de knop "Wachtwoord Vergeten" onderin de login pagina.')
            ->line('Als u vragen heeft, neem dan contact met ons op via het contactformulier op de website.')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }
}
