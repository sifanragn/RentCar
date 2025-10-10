<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_hp',
        'alamat',
        'foto_ktp',
        'foto_kk',
        'role',
        'status_verifikasi'
    ];

    protected $hidden = [
        'password',
    ];

    // Cek apakah user ini admin (untuk login kombinasi)
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'user_id', 'user_id');
    }

    public function isVerified()
    {
        return $this->status_verifikasi === 'disetujui';
    }

}
