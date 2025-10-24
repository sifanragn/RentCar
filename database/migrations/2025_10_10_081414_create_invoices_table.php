<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->dateTime('tanggal_cetak')->useCurrent();
            $table->decimal('total_tagihan', 12, 2)->nullable();
            $table->enum('status_pengembalian', ['tepat_waktu', 'telat', 'rusak'])->default('tepat_waktu');
            $table->enum('status_invoice', ['pending', 'selesai', 'dibatalkan'])->default('pending');
            $table->decimal('denda_tambahan', 12, 2)->nullable();
            $table->decimal('total_akhir', 12, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();

            $table->foreign('rental_id')->references('rental_id')->on('rentals')->cascadeOnDelete();
            $table->foreign('admin_id')->references('admin_id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
