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
        Schema::table('car_brands', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('nama_merek');
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('car_brands', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
