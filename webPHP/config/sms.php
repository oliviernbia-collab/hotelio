<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver SMS
    |--------------------------------------------------------------------------
    | log            → log dans storage/logs/laravel.log (développement)
    | orange         → API Orange SMS CI (nécessite ORANGE_API_TOKEN)
    | africastalking → Africa's Talking (nécessite AT_USERNAME + AT_API_KEY)
    */
    'driver' => env('SMS_DRIVER', 'log'),

    'orange' => [
        'token'     => env('ORANGE_API_TOKEN'),
        'sender_id' => env('ORANGE_SENDER_ID', 'HotelioCI'),
    ],

    'africastalking' => [
        'username' => env('AT_USERNAME'),
        'api_key'  => env('AT_API_KEY'),
        'sender'   => env('AT_SENDER_ID', 'HotelioCI'),
    ],
];
