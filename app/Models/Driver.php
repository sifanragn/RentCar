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
        'harga_per_jam',
        'pengalaman',
        'lokasi',
        'deskripsi',
        'created_by',
    ];
    protected $appends = [
    'foto_url',
    'foto_sim_url',
    'foto_ktp_url',
    'foto_kk_url',
    'harga_formatted',
    'pengalaman_formatted',
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
    if (isset($this->harga_per_jam)) {
        return 'Rp' . number_format($this->harga_per_jam, 0, ',', '.'). ' / jam';
    }
    return '-';
}

    public function getPengalamanFormattedAttribute()
    {
        return $this->pengalaman ?: '-';
    }

    public function isAvailable($start, $end)
    {
        return !$this->rentals()
            ->whereIn('status_rental', [
                'draft',
                'menunggu_pembayaran',
                'berjalan'
            ])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_mulai', [$start, $end])
                ->orWhereBetween('tanggal_selesai', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('tanggal_mulai', '<=', $start)
                        ->where('tanggal_selesai', '>=', $end);
                });
            })
            ->exists();
    }


public function getStatusOperasionalAttribute()
{
    $now = now();

    // ❌ kalau nonaktif
    if ($this->status !== 'aktif') {
        return 'offline';
    }

    // 🔴 ON TRIP (lagi jalan sekarang)
    $onTrip = $this->rentals()
        ->where('status_rental', 'berjalan')
        ->where('tanggal_mulai', '<=', $now)
        ->where('tanggal_selesai', '>=', $now)
        ->exists();

    if ($onTrip) {
        return 'on_trip';
    }

    // 🟡 SCHEDULED (ada jadwal ke depan)
    $scheduled = $this->rentals()
        ->whereIn('status_rental', ['draft','menunggu_pembayaran'])
        ->where('tanggal_mulai', '>', $now)
        ->exists();

    if ($scheduled) {
        return 'scheduled';
    }

    // 🟢 AVAILABLE
    return 'available';
}

}
