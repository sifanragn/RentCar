<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_admin', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('aksi', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal')->useCurrent();

            $table->foreign('admin_id')->references('admin_id')->on('admins')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_admin');
    }
};
