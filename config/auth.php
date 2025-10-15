<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        // guard default untuk user biasa
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // guard khusus untuk admin panel
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        // provider untuk user biasa (tabel users)
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // provider untuk admin (tabel admins)
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table' => 'admin_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
