<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // tambahkan kolom baru setelah durasi_hari
            if (!Schema::hasColumn('rentals', 'durasi_jam')) {
                $table->integer('durasi_jam')->nullable()->after('durasi_hari');
            }

            // opsional: ubah nama kolom harga_driver_per_hari → harga_driver_per_jam nanti kalau mau full jam-based
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'durasi_jam')) {
                $table->dropColumn('durasi_jam');
            }
        });
    }
};
