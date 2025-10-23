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
        'foto_sim',
        'foto_ktp',
        'foto_kk',
        'sim_number',
        'status_verifikasi',
        'status',
        'harga_per_hari',
        'pengalaman',
        'lokasi',
        'deskripsi',
        'created_by',
    ];

    /* ============================
       🔗 RELASI
    ============================ */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'driver_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /* ============================
       📸 ACCESSOR URL FOTO
    ============================ */
    public function getFotoUrlAttribute()
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default-driver.png');
    }

    public function getFotoSimUrlAttribute()
    {
        return $this->foto_sim
            ? asset('storage/' . $this->foto_sim)
            : null;
    }

    public function getFotoKtpUrlAttribute()
    {
        return $this->foto_ktp
            ? asset('storage/' . $this->foto_ktp)
            : null;
    }

    public function getFotoKkUrlAttribute()
    {
        return $this->foto_kk
            ? asset('storage/' . $this->foto_kk)
            : null;
    }

    /* ============================
       💰 FORMAT & SCOPE
    ============================ */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                     ->where('status_verifikasi', 'disetujui');
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp' . number_format($this->harga_per_hari, 0, ',', '.');
    }

    public function getPengalamanFormattedAttribute()
    {
        return $this->pengalaman ?: '-';
    }
}
