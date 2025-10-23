<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
    $table->id('driver_id');
    $table->string('nama', 100);
    $table->string('no_hp', 20)->nullable();
    $table->string('email', 100)->nullable();
    $table->string('foto')->nullable();
    $table->string('sim_number', 50)->nullable(); // Nomor SIM
    $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak'])->default('disetujui');
    $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
    $table->decimal('harga_per_hari', 12, 2)->default(150000);
    $table->string('pengalaman', 100)->nullable(); // Contoh: "5 tahun pengalaman"
    $table->string('lokasi', 100)->nullable();     // Area kerja
    $table->text('deskripsi')->nullable();         // Catatan tambahan
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
