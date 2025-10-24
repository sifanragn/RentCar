<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('no_transaksi', 20)->nullable();
            $table->unsignedBigInteger('rental_id')->nullable(); // ✅ sama dengan rentals.rental_id
            $table->string('gateway', 50)->default('offline');
            $table->string('metode', 50)->nullable();
            $table->string('payment_type', 50)->default('main');
            $table->decimal('total_bayar', 12, 2)->nullable();
            $table->string('status_pembayaran', 20)->default('pending');
            $table->string('gateway_reference', 100)->nullable();
            $table->string('merchant_order_id', 100)->nullable();
            $table->string('payment_token', 255)->nullable();
            $table->string('callback_status', 50)->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('rental_id')->references('rental_id')->on('rentals')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
