<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Pastikan kolom lama memang ada sebelum rename
            if (Schema::hasColumn('drivers', 'harga_per_hari')) {
                $table->renameColumn('harga_per_hari', 'harga_per_jam');
            }
        });
    }

    /**
     * Batalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'harga_per_jam')) {
                $table->renameColumn('harga_per_jam', 'harga_per_hari');
            }
        });
    }
};
