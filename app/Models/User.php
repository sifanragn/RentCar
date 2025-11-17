<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 🟩 Tambahkan ini untuk support API token
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
        'status_verifikasi',
        'instagram_id', 'instagram_username', 'instagram_token',
        'facebook_id', 'facebook_name', 'facebook_token', 'facebook_email', 'facebook_avatar', 'facebook_link',
        'tiktok_id', 'tiktok_username', 'tiktok_token',
        'discord_id', 'discord_username', 'discord_global_name', 'discord_email' , 'discord_avatar', 'discord_profile_url', 'discord_token',
    ];

    protected $hidden = [
        'password',
    ];

    // 🔹 Cek apakah user ini admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // 🔹 Relasi ke rental
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'user_id', 'user_id');
    }

    // 🔹 Cek verifikasi
    public function isVerified()
    {
        return $this->status_verifikasi === 'disetujui';
    }

    public function verificationLabel()
    {
        return match($this->status_verifikasi) {
            'disetujui' => '✅ Terverifikasi',
            'menunggu' => '⏳ Menunggu Verifikasi',
            'ditolak' => '❌ Ditolak',
            default => 'Belum Upload',
        };
    }

    public function verificationColor()
    {
        return match($this->status_verifikasi) {
            'disetujui' => 'success',
            'menunggu' => 'warning',
            'ditolak' => 'danger',
            default => 'secondary',
        };
    }

}
