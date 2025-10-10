<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarCapacity extends Model
{
    protected $table = 'car_capacities';
    protected $primaryKey = 'capacity_id';
    protected $fillable = ['jumlah_orang'];

    public function cars()
    {
        return $this->hasMany(Car::class, 'capacity_id');
    }
}
