<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('invoices', 'charge'); // 🔥 INI YANG BENAR
    }

    public function down(): void
    {
        Schema::rename('charge', 'invoices'); // rollback
    }
};
