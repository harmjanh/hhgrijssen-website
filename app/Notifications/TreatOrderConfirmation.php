<?php

namespace App\Notifications;

use App\Models\TreatOrder;
use App\Notifications\Concerns\FormatsTreatOrderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TreatOrderConfirmation extends Notification implements ShouldQueue
{
    use FormatsTreatOrderMail;
    use Queueable;

    public function __construct(
        protected TreatOrder $order
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Bevestiging snoep- en stroopwafelbestelling #' . $this->order->id)
            ->greeting('Beste ' . $this->order->name)
            ->line('Bedankt voor uw bestelling! Uw betaling is succesvol ontvangen.')
            ->line('')
            ->line('Overzicht van uw bestelling:');

        return $this->appendTreatOrderDetails($mail, $this->order)
            ->line('')
            ->line('We zien u graag op de jeugd- en gemeentedag.')
            ->line('')
            ->line('Met vriendelijke groet,')
            ->line('HHG Rijssen');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'treat_order_id' => $this->order->id,
        ];
    }
}
