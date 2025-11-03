<?php

namespace App\Helpers;

class PaymentConfig
{
    public static function duitku()
    {
        $isLocalHost = request()->getHost() === '127.0.0.1' 
                    || request()->getHost() === 'localhost' 
                    || str_contains(request()->getHost(), 'ngrok-free.app')
                    || env('PAYMENT_ENV') === 'local';

        return [
            'merchant_code' => $isLocalHost 
                ? env('DUITKU_MERCHANT_CODE_LOCAL') 
                : env('DUITKU_MERCHANT_CODE_PROD'),

            'api_key' => $isLocalHost 
                ? env('DUITKU_API_KEY_LOCAL') 
                : env('DUITKU_API_KEY_PROD'),

            'callback_url' => $isLocalHost 
                ? env('DUITKU_CALLBACK_URL_LOCAL') 
                : env('DUITKU_CALLBACK_URL_PROD')
        ];
    }
}
