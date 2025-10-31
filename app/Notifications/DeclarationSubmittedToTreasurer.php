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
        $name = $user ? $user->name : $declaration->name;

        // Calculate amounts for display
        $timeslotPrice = 130.00;
        $kilometerPrice = 0.35;
        $numberOfTimeslots = (!empty($declaration->time_of_service_1) && !empty($declaration->time_of_service_2)) ? 2 : 1;
        $timeslotTotal = $numberOfTimeslots * $timeslotPrice;
        $kilometerTotal = $declaration->kilometers * $kilometerPrice;

        $mailMessage = (new MailMessage)
            ->subject('Nieuwe declaratie ontvangen')
            ->greeting('Beste penningmeester')
            ->line('Er is een nieuwe declaratie ingediend door ' . $name . '.')
            ->line('Details van de declaratie:')
            ->line('Naam: ' . $declaration->name)
            ->line('E-mail: ' . $declaration->email)
            ->line('Adres: ' . $declaration->street . ' ' . $declaration->number)
            ->line('Postcode: ' . $declaration->zipcode . ' ' . $declaration->city)
            ->line('Datum van dienst: ' . ($declaration->date_of_service ? $declaration->date_of_service->format('d-m-Y') : 'N/A'))
            ->line('Tijd van dienst: ' . ($declaration->time_of_service_1 ? $declaration->time_of_service_1->format('H:i') : 'N/A'));

        if ($declaration->time_of_service_2) {
            $mailMessage->line('Tijd van dienst 2: ' . $declaration->time_of_service_2->format('H:i'));
        }

        $mailMessage->line('Kilometers: ' . $declaration->kilometers)
            ->line('')
            ->line('Declaratie berekening:')
            ->line('Aantal diensten: ' . $numberOfTimeslots . ' x €' . number_format($timeslotPrice, 2) . ' = €' . number_format($timeslotTotal, 2))
            ->line('Kilometers: ' . $declaration->kilometers . ' km x €' . number_format($kilometerPrice, 2) . ' = €' . number_format($kilometerTotal, 2))
            ->line('Totaal declaratie: €' . number_format($declaration->amount, 2))
            ->line('')
            ->line('Omschrijving: ' . $declaration->explanation)
            ->action('Bekijk declaratie', route('filament.admin.resources.declarations.view', $declaration))
            ->line('Gelieve deze declaratie te verwerken.');

        return $mailMessage;
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
