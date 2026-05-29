<?php

namespace App\Notifications\Concerns;

use App\Models\TreatOrder;
use Illuminate\Notifications\Messages\MailMessage;

trait FormatsTreatOrderMail
{
    protected function appendTreatOrderDetails(MailMessage $mail, TreatOrder $order): MailMessage
    {
        $mail->line('Bestelnummer: #' . $order->id)
            ->line('Naam: ' . $order->name)
            ->line('E-mailadres: ' . $order->email)
            ->line('Telefoonnummer: ' . $order->phone)
            ->line('')
            ->line('Bestelling:');

        foreach ($order->orderLineDescriptions() as $line) {
            $mail->line('• ' . $line);
        }

        return $mail
            ->line('')
            ->line('Totaalbedrag (incl. betaalkosten): €' . number_format($order->total_amount, 2, ',', '.'))
            ->line('')
            ->line('Ophalen op ' . TreatOrder::pickupDateFormatted() . ' tijdens de jeugd- en gemeentedag, ' . config('treats.pickup_location') . '.');
    }
}
