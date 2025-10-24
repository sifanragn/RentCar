<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id('driver_id');
            $table->string('nama', 100);
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('foto_sim', 255)->nullable();
            $table->string('foto_ktp', 255)->nullable();
            $table->string('foto_kk', 255)->nullable();
            $table->string('sim_number', 50)->nullable();
            $table->enum('status_verifikasi', ['disetujui'])->default('disetujui');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->decimal('harga_per_hari', 12, 2)->default(150000.00);
            $table->string('pengalaman', 100)->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
