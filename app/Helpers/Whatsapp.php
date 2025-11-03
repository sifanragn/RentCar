<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Whatsapp
{
    public static function send($phone, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('FONNTE_API_KEY'),
            ])->asForm()->post(env('FONNTE_URL'), [
                'target'  => $phone,
                'message' => $message,
            ]);

            // ✅ cek Fonnte sukses
            return $response->successful() && 
                   ($response->json()['status'] ?? false) === true;

        } catch (\Exception $e) {
            \Log::error("WA Send Error: ".$e->getMessage());
            return false;
        }
    }

    public static function sendFile($to, $fileBinary, $fileName, $caption = '')
    {
        try {
            $response = Http::withToken(env('FONNTE_TOKEN'))
                ->attach('file', $fileBinary, $fileName)
                ->post('https://api.fonnte.com/send', [
                    'target'  => $to,
                    'caption' => $caption
                ]);

            return $response->successful() && 
                   ($response->json()['status'] ?? false) === true;

        } catch (\Exception $e) {
            \Log::error("WA File Error: ".$e->getMessage());
            return false;
        }
    }
}
