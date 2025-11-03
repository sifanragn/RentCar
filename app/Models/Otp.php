<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Helpers\PhoneFormatter;

class Otp extends Model
{
    protected $fillable = ['phone', 'code', 'expires_at'];

    public $timestamps = true;

    public static function generate($phone)
    {
        // ✅ pastikan format konsisten (08xx → 628xx)
        $phone = PhoneFormatter::format($phone);

        // hapus OTP sebelumnya
        self::where('phone', $phone)->delete();

        // generate otp
        $code = rand(100000, 999999);

        return self::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);
    }
}
