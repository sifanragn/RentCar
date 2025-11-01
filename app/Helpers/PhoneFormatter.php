<?php

namespace App\Helpers;

class PhoneFormatter
{
    public static function format($phone)
    {
        // hilangkan spasi, strip, titik
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika mulai dari 0 → ubah ke 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum ada 62 → tambahkan
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
