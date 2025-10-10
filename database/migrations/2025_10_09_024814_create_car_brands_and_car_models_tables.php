<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel merek mobil
        Schema::create('car_brands', function (Blueprint $table) {
            $table->bigIncrements('brand_id'); // ✅ gunakan bigIncrements untuk PK
            $table->string('nama_merek')->unique();
            $table->timestamps();
        });

        // Tabel model mobil
        Schema::create('car_models', function (Blueprint $table) {
            $table->bigIncrements('model_id');
            // 🔧 perbaiki bagian foreign key-nya
            $table->unsignedBigInteger('brand_id');
            $table->string('nama_model');
            $table->timestamps();

            // ✅ arahkan ke kolom brand_id, bukan id
            $table->foreign('brand_id')
                  ->references('brand_id')
                  ->on('car_brands')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_models');
        Schema::dropIfExists('car_brands');
    }
};
