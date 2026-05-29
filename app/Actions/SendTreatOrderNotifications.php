<?php

namespace App\Actions;

use App\Models\TreatOrder;
use App\Notifications\TreatOrderConfirmation;
use App\Notifications\TreatOrderReceived;
use Illuminate\Support\Facades\Notification;

class SendTreatOrderNotifications
{
    public function execute(TreatOrder $order): void
    {
        Notification::route('mail', $order->email)
            ->notify(new TreatOrderConfirmation($order));

        Notification::route('mail', config('treats.notification_email'))
            ->notify(new TreatOrderReceived($order));
    }
}
