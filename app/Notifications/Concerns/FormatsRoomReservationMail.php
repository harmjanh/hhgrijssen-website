<?php

namespace App\Notifications\Concerns;

use App\Models\RoomReservation;
use Illuminate\Notifications\Messages\MailMessage;

trait FormatsRoomReservationMail
{
    protected function formatBoolean(?bool $value): string
    {
        return match ($value) {
            true => 'Ja',
            false => 'Nee',
            default => 'Onbekend',
        };
    }

    protected function addReservationDetails(
        MailMessage $mailMessage,
        RoomReservation $reservation,
        bool $includeUserDetails = true
    ): MailMessage {
        $reservation->loadMissing(['user', 'room']);

        $user = $reservation->user;
        $room = $reservation->room;

        $mailMessage
            ->line('**Details van de reservering:**');

        if ($includeUserDetails) {
            $mailMessage
                ->line('**Gebruiker:** ' . ($user ? $user->name : 'Onbekend'))
                ->line('**E-mail:** ' . ($user ? $user->email : 'Onbekend'));
        }

        return $mailMessage
            ->line('**Zaal:** ' . ($room ? $room->name : 'Onbekend'))
            ->line('**Onderwerp:** ' . $reservation->subject)
            ->line('**Aantal personen:** ' . $reservation->number_of_people)
            ->line('**Starttijd:** ' . $reservation->start_time->format('d-m-Y H:i'))
            ->line('**Eindtijd:** ' . $reservation->end_time->format('d-m-Y H:i'))
            ->line('**Koffie gewenst:** ' . $this->formatBoolean($reservation->coffee_needed))
            ->line('**Pauze:** ' . $this->formatBoolean($reservation->has_break))
            ->line('**Beamer nodig:** ' . $this->formatBoolean($reservation->beamer_needed))
            ->line('**Gastspreker:** ' . $this->formatBoolean($reservation->guest_speaker))
            ->line('**Uitzending:** ' . $this->formatBoolean($reservation->broadcast_needed))
            ->line('**Overige opmerkingen:** ' . ($reservation->other_remarks ?: 'Onbekend'));
    }
}
