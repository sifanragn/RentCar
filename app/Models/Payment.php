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
    static::creating(function ($payment) {
        // Hanya generate untuk pembayaran utama
        if ($payment->payment_type === 'main' && empty($payment->no_transaksi)) {
            $lastNumber = Payment::whereNotNull('no_transaksi')
                ->where('payment_type', 'main')
                ->orderByDesc('payment_id')
                ->value('no_transaksi');

            // Ambil angka terakhir dan tambahkan 1
            $nextNumber = 1;
            if ($lastNumber && preg_match('/INV(\d+)/', $lastNumber, $m)) {
                $nextNumber = intval($m[1]) + 1;
            }

            // Format INV0001
            $payment->no_transaksi = 'INV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        // Kalau ini charge, ambil no_transaksi dari payment utama
        if ($payment->payment_type === 'charge' && empty($payment->no_transaksi)) {
            $main = Payment::where('rental_id', $payment->rental_id)
                ->where('payment_type', 'main')
                ->first();
            if ($main) {
                $payment->no_transaksi = $main->no_transaksi;
            }
        }
    });
}

}

