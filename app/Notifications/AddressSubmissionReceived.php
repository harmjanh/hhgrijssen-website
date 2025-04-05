<?php

namespace App\Notifications;

use App\Models\AddressSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddressSubmissionReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected AddressSubmission $submission
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Adreswijziging Ontvangen')
            ->greeting('Beste ' . $this->submission->name)
            ->line('We hebben uw adreswijziging ontvangen. Hier zijn de details:')
            ->line('Huidig Adres:')
            ->line($this->submission->old_street . ' ' . $this->submission->old_number)
            ->line($this->submission->old_zipcode . ' ' . $this->submission->old_city)
            ->line('Nieuw Adres:')
            ->line($this->submission->new_street . ' ' . $this->submission->new_number)
            ->line($this->submission->new_zipcode . ' ' . $this->submission->new_city)
            ->line('We zullen uw wijziging zo spoedig mogelijk verwerken.')
            ->action('Bekijk Aanvraag', route('address-submissions.show', $this->submission));
    }
}
