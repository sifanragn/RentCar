<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarPhoto extends Model
{

    use HasFactory;

    protected $fillable = ['car_id', 'path'];
    protected $primaryKey = 'id'; // FIX DI SINI
    public $incrementing = true;
    protected $keyType = 'int';

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
