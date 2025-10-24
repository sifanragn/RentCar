<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_models', function (Blueprint $table) {
            $table->id('model_id');
            $table->unsignedBigInteger('brand_id');
            $table->string('nama_model', 255);
            $table->timestamps();

            $table->foreign('brand_id')->references('brand_id')->on('car_brands')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
};
