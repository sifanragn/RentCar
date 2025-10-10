<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'cars';
    protected $primaryKey = 'car_id';
    protected $fillable = [
        'brand_id',
        'model',
        'tahun',
        'warna',
        'tipe_transmisi',
        'capacity_id',
        'bahan_bakar',
        'harga_sewa_per_hari',
        'status',
        'lokasi',
        'kilometer',
        'liter_tangki',
        'deskripsi',
        'foto'
    ];

    // ==================== RELASI ====================

    public function brand()
    {
        return $this->belongsTo(CarBrand::class, 'brand_id', 'brand_id');
    }

    public function capacity()
    {
        return $this->belongsTo(CarCapacity::class, 'capacity_id', 'capacity_id');
    }
}
