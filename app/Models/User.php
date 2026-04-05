<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 🟩 Trait untuk API Token (Sanctum)
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /*
    |--------------------------------------------------------------------------
    | 📝 FILLABLE (FIELD YANG BOLEH DIISI)
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_hp',
        'alamat',
        'foto_ktp',
        'foto_kk',
        'foto_profil',
        'role',
        'status_verifikasi',

        // 🔗 Social Media
        'instagram_id', 'instagram_username', 'instagram_token',
        'facebook_id', 'facebook_name', 'facebook_token', 'facebook_email', 'facebook_avatar', 'facebook_link',
        'tiktok_id', 'tiktok_username', 'tiktok_token',
        'discord_id', 'discord_username', 'discord_global_name', 'discord_email', 'discord_avatar', 'discord_profile_url', 'discord_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔒 HIDDEN (TIDAK DITAMPILKAN)
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🧠 HELPER FUNCTION
    |--------------------------------------------------------------------------
    */

    // Cek apakah user adalah admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Cek apakah user sudah terverifikasi
    public function isVerified()
    {
        return $this->status_verifikasi === 'disetujui';
    }

    /*
    |--------------------------------------------------------------------------
    | 🔗 RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Relasi ke rental
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'user_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 🎨 FORMAT STATUS VERIFIKASI (UNTUK VIEW)
    |--------------------------------------------------------------------------
    */

    // Label status verifikasi (dengan icon)
    public function verificationLabel()
    {
        return match ($this->status_verifikasi) {
            'disetujui' => '✅ Terverifikasi',
            'menunggu'  => '⏳ Menunggu Verifikasi',
            'ditolak'   => '❌ Ditolak',
            default     => 'Belum Upload',
        };
    }

    // Warna badge/status (Bootstrap friendly)
    public function verificationColor()
    {
        return match ($this->status_verifikasi) {
            'disetujui' => 'success',
            'menunggu'  => 'warning',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }
}