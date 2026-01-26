<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject('Bevestig uw emailadres')
            ->greeting('Beste ' . ($notifiable->name ?? 'gebruiker'))
            ->line('Klik op onderstaande knop om uw emailadres te bevestigen.')
            ->action('Emailadres Bevestigen', $verificationUrl)
            ->line('Deze link verloopt over 60 minuten.')
            ->line('Als u geen account heeft aangemaakt, hoeft u verder niets te doen.')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }
}
