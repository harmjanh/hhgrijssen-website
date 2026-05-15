<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsContactFormSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected News $news,
        protected array $data
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Reactie via nieuwsbericht: ' . $this->news->title)
            ->greeting('Beste ontvanger,')
            ->replyTo($this->data['email'], $this->data['name'])
            ->line('Er is een reactie ontvangen via het contactformulier bij het nieuwsbericht "' . $this->news->title . '".')
            ->line('')
            ->line('**Naam:** ' . $this->data['name'])
            ->line('**E-mailadres:** ' . $this->data['email']);

        if (! empty($this->data['phone'])) {
            $message->line('**Telefoonnummer:** ' . $this->data['phone']);
        }

        return $message
            ->line('')
            ->line('**Opmerkingen:**')
            ->line($this->data['remarks'])
            ->line('')
            ->line('Verzonden op: ' . now()->format('d-m-Y H:i'));
    }
}
