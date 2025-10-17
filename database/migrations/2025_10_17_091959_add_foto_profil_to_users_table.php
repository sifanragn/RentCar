<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan foto profil kalau belum ada
            if (!Schema::hasColumn('users', 'foto_profil')) {
                $table->string('foto_profil')->nullable()->after('alamat');
            }

            // Pastikan kolom foto_ktp dan foto_kk ada
            if (!Schema::hasColumn('users', 'foto_ktp')) {
                $table->string('foto_ktp')->nullable()->after('foto_profil');
            }

            if (!Schema::hasColumn('users', 'foto_kk')) {
                $table->string('foto_kk')->nullable()->after('foto_ktp');
            }

            // Status verifikasi (buat jaga-jaga kalau belum ada)
            if (!Schema::hasColumn('users', 'status_verifikasi')) {
                $table->enum('status_verifikasi', [
                    'belum_upload', 'menunggu', 'disetujui', 'ditolak'
                ])->default('belum_upload')->after('foto_kk');
            }
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika rollback
            if (Schema::hasColumn('users', 'foto_profil')) {
                $table->dropColumn('foto_profil');
            }

            if (Schema::hasColumn('users', 'foto_ktp')) {
                $table->dropColumn('foto_ktp');
            }

            if (Schema::hasColumn('users', 'foto_kk')) {
                $table->dropColumn('foto_kk');
            }

            if (Schema::hasColumn('users', 'status_verifikasi')) {
                $table->dropColumn('status_verifikasi');
            }
        });
    }
};
