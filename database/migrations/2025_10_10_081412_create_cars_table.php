<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id('car_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('merek', 50)->nullable();
            $table->string('model', 100)->nullable();
            $table->integer('tahun')->nullable();
            $table->string('warna', 50)->nullable();
            $table->enum('tipe_transmisi', ['manual', 'otomatis'])->nullable();
            $table->integer('kapasitas_orang')->nullable();
            $table->unsignedBigInteger('capacity_id')->nullable();
            $table->enum('bahan_bakar', ['bensin', 'diesel', 'hybrid'])->nullable();
            $table->decimal('harga_sewa_per_hari', 12, 2)->nullable();
            $table->enum('status', ['tersedia', 'disewa', 'perawatan'])->default('tersedia');
            $table->string('lokasi', 100)->nullable();
            $table->integer('kilometer')->nullable();
            $table->integer('liter_tangki')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('capacity_id')->references('capacity_id')->on('car_capacities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
