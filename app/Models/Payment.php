<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // ⛔ Cast jadi string agar tidak diubah ke UTC oleh Laravel
    protected $casts = [
        'expired_at' => 'string',
        'tanggal_bayar' => 'string',
    ];

    protected $fillable = [
        'rental_id',
        'gateway',
        'metode',
        'payment_type',
        'total_bayar',
        'status_pembayaran',
        'gateway_reference',
        'payment_token',
        'callback_status',
        'tanggal_bayar',
        'expired_at'
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 RELASI
    |--------------------------------------------------------------------------
    */
    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class, 'rental_id', 'rental_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 🕒 FIX WAKTU: semua waktu dibaca sebagai WIB
    |--------------------------------------------------------------------------
    */
    public function getTanggalBayarAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Jakarta')
            : null;
    }

    public function getExpiredAtAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Jakarta')
            : null;
    }

    public function getTanggalBayarWibAttribute()
    {
        return $this->tanggal_bayar
            ? $this->tanggal_bayar->format('d M Y, H:i') . ' WIB'
            : '-';
    }

    /*
    |--------------------------------------------------------------------------
    | 🧭 AUTO LOG STATUS CHANGE (TANPA GANGGU CHARGE)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::updating(function ($payment) {
            if ($payment->isDirty('status_pembayaran')) {

                // 🚫 Blok update otomatis untuk charge
                if (
                    $payment->payment_type === 'charge' &&
                    in_array($payment->status_pembayaran, ['failed', 'cancelled', 'dibatalkan']) &&
                    !request()->is('admin/*')
                ) {
                    Log::warning("🚫 DIBLOK: Payment charge #{$payment->payment_id} dicegah (bukan admin)");
                    $payment->status_pembayaran = $payment->getOriginal('status_pembayaran');
                    return false;
                }

                // ✅ Catat perubahan biasa
                $old = $payment->getOriginal('status_pembayaran');
                $new = $payment->status_pembayaran;
                Log::info("⚠️ Payment #{$payment->payment_id} berubah dari '{$old}' ke '{$new}'");
            }
        });
    }
}
