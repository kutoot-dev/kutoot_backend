<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kutoot global settings
    |--------------------------------------------------------------------------
    |
    | coin_value: Rupees value for 1 coin
    |
    */
    'coin_value' => (float) env('KUTOOT_COIN_VALUE', 0.25),
    'currency_symbol' => env('KUTOOT_CURRENCY_SYMBOL', '₹'),
];


