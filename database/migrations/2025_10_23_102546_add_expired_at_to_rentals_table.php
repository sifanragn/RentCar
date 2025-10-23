<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom expired_at ke tabel rentals.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dateTime('expired_at')->nullable()->after('status_rental');
        });
    }

    /**
     * Hapus kolom expired_at kalau di-rollback.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
};
