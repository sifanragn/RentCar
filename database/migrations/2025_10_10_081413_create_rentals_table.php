<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->integer('durasi_hari')->nullable();
            $table->enum('metode_pickup', ['ambil_sendiri', 'pickup_alamat'])->nullable();
            $table->enum('driver', ['ya', 'tidak'])->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->decimal('harga_driver_per_hari', 12, 2)->default(0.00);
            $table->decimal('total_biaya', 12, 2)->nullable();
            $table->enum('status_rental', [
                'verifikasi_diperlukan',
                'menunggu_pembayaran',
                'berjalan',
                'selesai',
                'dibatalkan',
                'draft'
            ])->default('verifikasi_diperlukan');
            $table->dateTime('expired_at')->nullable();
            $table->date('tanggal_pengembalian')->nullable();
            $table->decimal('denda', 12, 2)->nullable();
            $table->text('catatan_admin')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('car_id')->references('car_id')->on('cars')->cascadeOnDelete();
            $table->foreign('driver_id')->references('driver_id')->on('drivers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
