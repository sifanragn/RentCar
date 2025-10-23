<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rental extends Model
{
    use HasFactory;

    protected $primaryKey = 'rental_id';

    protected $fillable = [
        'user_id',
        'car_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'metode_pickup',
        'driver',
        'driver_id',  // ✅ tambahkan ini
        'harga_driver_per_hari',
        'total_biaya',
        'status_rental',
        'tanggal_pengembalian',
        'denda',
        'catatan_admin',
        'expired_at', // 🟢 tambahkan ini
    ];

    /*
    |--------------------------------------------------------------------------
    | ❗ CAST WAKTU SEBAGAI STRING
    |--------------------------------------------------------------------------
    | Ini penting supaya Laravel tidak auto-konversi UTC → local.
    | Karena MySQL kita sudah simpan waktu lokal (WIB/WITA/WIT),
    | jadi kita paksa Carbon baca langsung sebagai waktu Indonesia.
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'tanggal_mulai'        => 'string',
        'tanggal_selesai'      => 'string',
        'tanggal_pengembalian' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'rental_id', 'rental_id');
    }

    public function payment()
    {
        // Ambil payment terbaru berdasarkan payment_id
        return $this->hasOne(\App\Models\Payment::class, 'rental_id', 'rental_id')
                    ->latestOfMany('payment_id');
    }
public function invoice()
{
    return $this->hasOne(\App\Models\Invoice::class, 'rental_id', 'rental_id');
}

public function invoices()
{
    return $this->hasMany(\App\Models\Invoice::class, 'rental_id', 'rental_id');
}



    /*
    |--------------------------------------------------------------------------
    | 🕒 AUTO KONVERSI WAKTU SESUAI ZONA INDONESIA
    |--------------------------------------------------------------------------
    */
    private function detectIndonesianTimezone(): string
    {
        $offset = now()->offsetHours;
        return match ($offset) {
            7 => 'Asia/Jakarta',   // WIB
            8 => 'Asia/Makassar',  // WITA
            9 => 'Asia/Jayapura',  // WIT
            default => 'Asia/Jakarta',
        };
    }

    public function getTanggalMulaiAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Jakarta')
            : null;
    }

    public function getTanggalSelesaiAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Jakarta')
            : null;
    }

    public function getTanggalPengembalianAttribute($value)
    {
        return $value
            ? Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Jakarta')
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | 🧭 FORMAT UNTUK TAMPILAN VIEW (WIB/WITA/WIT)
    |--------------------------------------------------------------------------
    */
    private function formatIndonesiaTime($datetime)
    {
        if (!$datetime) return '-';
        $dt = Carbon::parse($datetime);
        $offset = $dt->offsetHours;

        $label = match ($offset) {
            7 => 'WIB',
            8 => 'WITA',
            9 => 'WIT',
            default => 'WIB',
        };

        return $dt->format('d M Y, H:i') . " $label";
    }

    public function getTanggalMulaiWibAttribute()
    {
        return $this->formatIndonesiaTime($this->tanggal_mulai);
    }

    public function getTanggalSelesaiWibAttribute()
    {
        return $this->formatIndonesiaTime($this->tanggal_selesai);
    }

    public function getTanggalPengembalianWibAttribute()
    {
        return $this->formatIndonesiaTime($this->tanggal_pengembalian);
    }

    public function mainPayment()
{
    return $this->hasOne(Payment::class, 'rental_id')->where('payment_type', 'main');
}

// App\Models\Rental
public function driver()
{
    return $this->belongsTo(Driver::class, 'driver_id');
}

public function driverData()
{
    return $this->belongsTo(\App\Models\Driver::class, 'driver_id', 'driver_id');
}

}
