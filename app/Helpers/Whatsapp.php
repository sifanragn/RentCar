<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Whatsapp
{
    public static function send($phone, $message)
    {
        return Http::withHeaders([
            'Authorization' => env('FONNTE_API_KEY'),
        ])->asForm()->post(env('FONNTE_URL'), [
            'target' => $phone,
            'message' => $message,
        ]);
    }
    public static function sendFile($to, $fileBinary, $fileName, $caption = '')
{
    return Http::withToken(env('FONNTE_TOKEN'))
        ->attach('file', $fileBinary, $fileName)
        ->post('https://api.fonnte.com/send', [
            'target' => $to,
            'caption' => $caption
        ]);
}

}
