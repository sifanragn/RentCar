<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ganti Model -> Authenticatable
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';
    public $timestamps = true; // penting kalau ada created_at/updated_at

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_hp',
        'foto',
        'role',
        'status'
    ];

    protected $hidden = ['password'];
}
