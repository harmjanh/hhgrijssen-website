<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestRegistrationNumber extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $user
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
        $user = $this->user;

        return (new MailMessage)
            ->subject('Verzoek registratienummer - ' . $user->name)
            ->greeting('Beste penningmeester,')
            ->line('Er is een verzoek binnengekomen voor een registratienummer.')
            ->line('Gebruiker gegevens:')
            ->line('Naam: ' . $user->name)
            ->line('E-mail: ' . $user->email)
            ->line('')
            ->line('Gelieve het registratienummer naar bovenstaand e-mailadres te sturen.')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }
}
