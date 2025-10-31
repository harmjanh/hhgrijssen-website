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
        'silver_coin' => env('SILVER_COIN_PRICE', 0.75),
        'gold_coin' => env('GOLD_COIN_PRICE', 1.25),
        'payment_fee' => env('PAYMENT_FEE', 0.35),
    ],
];
