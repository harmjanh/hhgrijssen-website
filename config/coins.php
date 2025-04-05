<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Coin Prices
    |--------------------------------------------------------------------------
    |
    | Here you can configure the prices for different types of coins.
    |
    */
    'prices' => [
        'blue_coin' => env('BLUE_COIN_PRICE', 0.60),
        'red_coin' => env('RED_COIN_PRICE', 0.90),
        'payment_fee' => env('PAYMENT_FEE', 0.35),
    ],
];
