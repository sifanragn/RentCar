<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'car_models';
    protected $primaryKey = 'model_id';
    protected $fillable = ['nama_model', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(CarBrand::class, 'brand_id', 'brand_id');
    }
}
