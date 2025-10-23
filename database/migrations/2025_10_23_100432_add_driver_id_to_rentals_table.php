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
    Schema::table('rentals', function (Blueprint $table) {
        $table->unsignedBigInteger('driver_id')->nullable()->after('driver');
        $table->foreign('driver_id')->references('driver_id')->on('drivers')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('rentals', function (Blueprint $table) {
        $table->dropForeign(['driver_id']);
        $table->dropColumn('driver_id');
    });
}

};
