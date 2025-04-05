<?php

namespace App\Notifications;

use App\Models\Declaration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeclarationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Declaration $declaration
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
        $declaration = $this->declaration;
        $user = $declaration->user;

        return (new MailMessage)
            ->subject('Nieuwe declaratie ingediend')
            ->greeting('Beste ' . $user->name)
            ->line('Uw declaratie is succesvol ingediend.')
            ->line('Details van uw declaratie:')
            ->line('Bedrag: €' . number_format($declaration->amount, 2, ',', '.'))
            ->line('Omschrijving: ' . $declaration->explanation)
            ->line('Status: In behandeling')
            ->line('U ontvangt een bericht zodra uw declaratie is verwerkt.')
            ->action('Bekijk declaratie', route('declarations.show', $declaration))
            ->line('Bedankt voor uw declaratie.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'declaration_id' => $this->declaration->id,
            'amount' => $this->declaration->amount,
        ];
    }
}
