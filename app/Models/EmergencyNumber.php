<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyNumber extends Model
{
    protected $fillable = ['keperluan', 'nama', 'nomor', 'icon', 'keterangan'];
}
