<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    protected $table = 'car_brands';
    protected $primaryKey = 'brand_id';
    protected $fillable = ['nama_merek', 'logo']; // ✅ tambahkan logo

    // Relasi ke tabel car_models
    public function models()
    {
        return $this->hasMany(CarModel::class, 'brand_id', 'brand_id');
    }

    // ✅ Relasi ke tabel cars (biar bisa akses $brand->cars)
    public function cars()
    {
        return $this->hasMany(Car::class, 'brand_id', 'brand_id');
    }
}
