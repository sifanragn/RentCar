<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MIDTRANS CONFIGURATION
    |--------------------------------------------------------------------------
    |
    | Server key dan client key bisa dilihat di Dashboard Midtrans.
    | Pastikan kamu menggunakan key sandbox untuk pengujian.
    |
    */

    'merchant_id'   => env('MIDTRANS_MERCHANT_ID', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | MODE & SECURITY
    |--------------------------------------------------------------------------
    */

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false), // false = sandbox
    'is_sanitized'  => true,
    'is_3ds'        => true,
];
