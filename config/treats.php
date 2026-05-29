<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bestelperiode
    |--------------------------------------------------------------------------
    |
    | Bestellen kan tot en met de order_deadline (einde van die dag).
    | Na deze datum is de bestelpagina niet meer bereikbaar.
    |
    */
    'order_deadline' => env('TREAT_ORDER_DEADLINE', '2026-07-06'),
    'pickup_date' => env('TREAT_PICKUP_DATE', '2026-08-29'),
    'pickup_location' => 'Plasdijk 18, Markelo',
    'notification_email' => env('TREAT_ORDER_NOTIFICATION_EMAIL', 'hhhazelhorst@hhgrijssen.nl'),

    'prices' => [
        'snoeprollen' => 7.00,
        'stroopwafels' => 10.00,
        'payment_fee' => env('PAYMENT_FEE', 0.35),
    ],
    'units' => [
        'snoeprollen_per_order' => 10,
        'stroopwafels_packages_per_order' => 3,
    ],
];
