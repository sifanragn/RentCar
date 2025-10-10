<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    protected $table = 'car_brands';
    protected $primaryKey = 'brand_id';
    protected $fillable = ['nama_merek'];

    public function models()
    {
        return $this->hasMany(CarModel::class, 'brand_id', 'brand_id');
    }
}
