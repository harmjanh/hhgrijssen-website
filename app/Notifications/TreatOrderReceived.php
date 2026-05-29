<?php

namespace App\Notifications;

use App\Models\TreatOrder;
use App\Notifications\Concerns\FormatsTreatOrderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TreatOrderReceived extends Notification implements ShouldQueue
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
            ->subject('Nieuwe snoep- en stroopwafelbestelling #' . $this->order->id)
            ->greeting('Beste')
            ->replyTo($this->order->email, $this->order->name)
            ->line('Er is een nieuwe bestelling geplaatst en betaald via de website.')
            ->line('')
            ->line('Gegevens van de bestelling:');

        $mail = $this->appendTreatOrderDetails($mail, $this->order);

        if (filled($this->order->remarks)) {
            $mail->line('')
                ->line('Opmerkingen:')
                ->line($this->order->remarks);
        }

        return $mail
            ->line('')
            ->line('Besteld op: ' . $this->order->created_at->format('d-m-Y H:i'))
            ->line('')
            ->line('HHG Rijssen Website');
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
