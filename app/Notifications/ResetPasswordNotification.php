<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
        $url = URL::route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Wachtwoord Resetten')
            ->greeting('Beste ' . $notifiable->name)
            ->line('U ontvangt deze e-mail omdat we een verzoek hebben ontvangen om uw wachtwoord te resetten.')
            ->action('Wachtwoord Resetten', $url)
            ->line('Deze wachtwoord reset link verloopt over 60 minuten.')
            ->line('Als u geen verzoek heeft gedaan om uw wachtwoord te resetten, hoeft u verder niets te doen.')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }
}
