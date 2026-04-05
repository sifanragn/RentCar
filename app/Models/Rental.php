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
        'durasi_jam',
        'metode_pickup',
        'driver',
        'driver_id', // relasi ke tabel driver
        'harga_driver_per_hari',
        'total_biaya',
        'status_rental',
        'tanggal_pengembalian',
        'denda',
        'catatan_admin',
        'expired_at', // waktu expired pembayaran / booking
    ];

    /*
    |--------------------------------------------------------------------------
    | ⚠️ CAST WAKTU SEBAGAI STRING
    |--------------------------------------------------------------------------
    | Supaya Laravel tidak otomatis konversi timezone (UTC → local).
    | Karena database sudah menyimpan waktu lokal Indonesia,
    | kita paksa tetap dibaca sebagai string dulu.
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

    // Relasi ke mobil
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Semua payment terkait rental
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'rental_id', 'rental_id');
    }

    // Ambil payment terbaru
    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class, 'rental_id', 'rental_id')
                    ->latestOfMany('payment_id');
    }

    // Relasi invoice (1)
    public function invoice()
    {
        return $this->hasOne(\App\Models\Invoice::class, 'rental_id', 'rental_id');
    }

    // Relasi invoice (banyak)
    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'rental_id', 'rental_id');
    }

    // Ambil payment utama (main payment)
    public function mainPayment()
    {
        return $this->hasOne(Payment::class, 'rental_id')
                    ->where('payment_type', 'main');
    }

    // Relasi driver (versi standar)
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // Relasi driver (custom key lengkap)
    public function driverData()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'driver_id', 'driver_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 🕒 KONVERSI STRING → CARBON (WAKTU INDONESIA)
    |--------------------------------------------------------------------------
    */

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
    | 🧭 FORMAT TAMPILAN WAKTU (WIB / WITA / WIT)
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

    /*
    |--------------------------------------------------------------------------
    | 🌏 DETEKSI TIMEZONE INDONESIA (TIDAK DIPAKAI SAAT INI)
    |--------------------------------------------------------------------------
    | Fungsi ini disiapkan jika nanti ingin auto-detect zona waktu user
    | berdasarkan offset server / client.
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
}