<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_history', function (Blueprint $table) {
            $table->id('history_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->dateTime('tanggal')->useCurrent();
            $table->enum('status', ['pending', 'paid', 'dibatalkan', 'selesai'])->default('pending');
            $table->text('deskripsi')->nullable();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('payment_id')->references('payment_id')->on('payments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_history');
    }
};
