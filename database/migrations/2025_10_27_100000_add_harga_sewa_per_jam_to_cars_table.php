<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // kalau kolom harga_sewa_per_hari ada, hapus
            if (Schema::hasColumn('cars', 'harga_sewa_per_hari')) {
                $table->dropColumn('harga_sewa_per_hari');
            }

            // tambah kolom harga_sewa_per_jam
            if (!Schema::hasColumn('cars', 'harga_sewa_per_jam')) {
                $table->decimal('harga_sewa_per_jam', 12, 2)->default(0)->after('bahan_bakar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'harga_sewa_per_jam')) {
                $table->dropColumn('harga_sewa_per_jam');
            }
            $table->decimal('harga_sewa_per_hari', 12, 2)->nullable()->after('bahan_bakar');
        });
    }
};
