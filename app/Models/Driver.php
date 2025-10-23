<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';
    protected $primaryKey = 'driver_id';

    protected $fillable = [
        'nama',
        'no_hp',
        'email',
        'foto',
        'sim_number',
        'status_verifikasi',
        'status',
        'harga_per_hari',
        'pengalaman',
        'lokasi',
        'deskripsi',
        'created_by',
    ];

    /**
     * 🔗 Relasi ke Rental
     * Setiap driver bisa punya banyak rental.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'driver_id');
    }

    /**
     * 🧑‍💻 Relasi ke admin yang menambahkan driver (opsional)
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * 📸 Accessor: Ambil URL foto lengkap
     */
    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return asset('images/default-driver.png'); // fallback foto default
        }
        return asset('storage/' . $this->foto);
    }

    /**
     * 🔍 Scope: hanya driver aktif & terverifikasi
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                     ->where('status_verifikasi', 'disetujui');
    }

    /**
     * 💰 Format harga otomatis (contoh: Rp150.000)
     */
    public function getHargaFormattedAttribute()
    {
        return 'Rp' . number_format($this->harga_per_hari, 0, ',', '.');
    }

    /**
     * 🕓 Format pengalaman (misal: "5 tahun" atau "-")
     */
    public function getPengalamanFormattedAttribute()
    {
        return $this->pengalaman ?: '-';
    }
}
