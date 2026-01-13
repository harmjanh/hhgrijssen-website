<?php

namespace App\Notifications;

use App\Models\AddressSubmission;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAddressSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected AddressSubmission $submission
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
        $submission = $this->submission;
        $user = $submission->user;

        return (new MailMessage)
            ->subject('Nieuwe Adreswijziging - ' . ($user ? $user->name : 'Onbekend') . ' - ' . $submission->created_at->format('d-m-Y'))
            ->greeting('Ha Arjan,')
            ->line('Er is een nieuwe adreswijziging ingediend.')
            ->line('Details:')
            ->line('Ingediend door: ' . ($user ? $user->name : 'Onbekend'))
            ->line('Datum indiening: ' . $submission->created_at->format('d-m-Y H:i'))
            ->line('')
            ->line('Naam: ' . $submission->name)
            ->line('E-mail: ' . $submission->email)
            ->line('Geboortedatum: ' . ($submission->date_of_birth ? (is_string($submission->date_of_birth) ? Carbon::parse($submission->date_of_birth)->format('d-m-Y') : $submission->date_of_birth->format('d-m-Y')) : 'Niet opgegeven'))
            ->line('Telefoonnummer: ' . ($submission->phone_number ?? 'Niet opgegeven'))
            ->line('')
            ->line('Oud Adres:')
            ->line('Straat: ' . $submission->old_street)
            ->line('Nummer: ' . $submission->old_number)
            ->line('Postcode: ' . $submission->old_zipcode)
            ->line('Woonplaats: ' . $submission->old_city)
            ->line('')
            ->line('Nieuw Adres:')
            ->line('Straat: ' . $submission->new_street)
            ->line('Nummer: ' . $submission->new_number)
            ->line('Postcode: ' . $submission->new_zipcode)
            ->line('Woonplaats: ' . $submission->new_city)
            ->line('')
            ->when($submission->other_people, function ($mailMessage) use ($submission) {
                return $mailMessage->line('Andere personen: ' . $submission->other_people);
            })
            ->when($submission->notes, function ($mailMessage) use ($submission) {
                return $mailMessage->line('Opmerkingen: ' . $submission->notes);
            })
            ->action('Bekijk Aanvraag', route('address-submissions.show', $submission));
    }
}

