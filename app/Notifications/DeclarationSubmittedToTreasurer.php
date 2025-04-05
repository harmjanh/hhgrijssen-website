<?php

namespace App\Notifications;

use App\Models\Declaration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeclarationSubmittedToTreasurer extends Notification implements ShouldQueue
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
            ->subject('Nieuwe declaratie ontvangen')
            ->greeting('Beste penningmeester')
            ->line('Er is een nieuwe declaratie ingediend door ' . $user->name . '.')
            ->line('Details van de declaratie:')
            ->line('Naam: ' . $declaration->name)
            ->line('Adres: ' . $declaration->street . ' ' . $declaration->number)
            ->line('Postcode: ' . $declaration->zipcode . ' ' . $declaration->city)
            ->line('Bankrekening: ' . $declaration->bankaccountnumber)
            ->line('Bedrag: €' . number_format($declaration->amount, 2, ',', '.'))
            ->line('Omschrijving: ' . $declaration->explanation)
            ->action('Bekijk declaratie', route('filament.admin.resources.declarations.view', $declaration))
            ->line('Gelieve deze declaratie te verwerken.');
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
