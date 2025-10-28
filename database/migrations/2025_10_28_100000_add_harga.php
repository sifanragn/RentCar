<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'harga_sewa_per_hari')) {
                $table->renameColumn('harga_sewa_per_hari', 'harga_sewa_per_jam');
            }
        });
    }

    public function down(): void {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'harga_sewa_per_jam')) {
                $table->renameColumn('harga_sewa_per_jam', 'harga_sewa_per_hari');
            }
        });
    }
};
