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

        // Check if this is a public declaration (no user_id) or authenticated user declaration
        $isPublicDeclaration = $declaration->isPublic();

        // Determine the date to use in subject
        $date = ($declaration->date_of_service && $declaration->date_of_service instanceof \Carbon\Carbon)
            ? $declaration->date_of_service->format('d-m-Y')
            : $declaration->created_at->format('d-m-Y');

        // Build subject with name and date
        $subject = $isPublicDeclaration
            ? 'Preekbeurt declaratie - ' . $name . ' (' . $date . ')'
            : 'Nieuwe declaratie ontvangen - ' . $name . ' (' . $date . ')';

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('Beste penningmeester,')
            ->line('Er is een nieuwe declaratie ingediend door ' . $name . '.')
            ->line('Details van de declaratie:')
            ->line('Naam: ' . $declaration->name);

        // Email is only available for public declarations
        if ($declaration->email) {
            $mailMessage->line('E-mail: ' . $declaration->email);
        }

        $mailMessage->line('Adres: ' . $declaration->street . ' ' . $declaration->number)
            ->line('Postcode: ' . $declaration->zipcode . ' ' . $declaration->city);

        // Only show date and time of service for public declarations and only if filled in
        if ($isPublicDeclaration) {
            if ($declaration->date_of_service && $declaration->date_of_service instanceof \Carbon\Carbon) {
                $mailMessage->line('Datum van dienst: ' . $declaration->date_of_service->format('d-m-Y'));
            }

            if ($declaration->time_of_service_1) {
                $mailMessage->line('Tijd van dienst: ' . $declaration->time_of_service_1);
            }

            if ($declaration->time_of_service_2) {
                $mailMessage->line('Tijd van dienst 2: ' . $declaration->time_of_service_2);
            }
        }

        // Only show kilometers and service calculations for public declarations
        // and only if the fields are filled in
        if ($isPublicDeclaration) {
            // Calculate amounts for display (only for public declarations)
            $timeslotPrice = 130.00;
            $kilometerPrice = 0.35;
            $numberOfTimeslots = (!empty($declaration->time_of_service_1) && !empty($declaration->time_of_service_2)) ? 2 : 1;
            $timeslotTotal = $numberOfTimeslots * $timeslotPrice;

            // Only show kilometers if it's filled in and greater than 0
            if (!empty($declaration->kilometers) && $declaration->kilometers > 0) {
                $kilometerTotal = $declaration->kilometers * $kilometerPrice;
                $mailMessage->line('Kilometers: ' . $declaration->kilometers);
            }

            $mailMessage->line('')
                ->line('Declaratie berekening:')
                ->line('Aantal diensten: ' . $numberOfTimeslots . ' x €' . number_format($timeslotPrice, 2) . ' = €' . number_format($timeslotTotal, 2));

            // Only show kilometers calculation if kilometers are filled in
            if (!empty($declaration->kilometers) && $declaration->kilometers > 0) {
                $kilometerTotal = $declaration->kilometers * $kilometerPrice;
                $mailMessage->line('Kilometers: ' . $declaration->kilometers . ' km x €' . number_format($kilometerPrice, 2) . ' = €' . number_format($kilometerTotal, 2));
            }

            $mailMessage->line('Totaal declaratie: €' . number_format($declaration->amount, 2));
        } else {
            // For authenticated user declarations, just show the total amount
            $mailMessage->line('Totaal declaratie: €' . number_format($declaration->amount, 2));
        }

        $mailMessage->line('')
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
