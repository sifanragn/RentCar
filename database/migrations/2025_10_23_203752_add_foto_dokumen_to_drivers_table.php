<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'foto_ktp')) {
                $table->string('foto_ktp')->nullable()->after('foto');
            }
            if (!Schema::hasColumn('drivers', 'foto_kk')) {
                $table->string('foto_kk')->nullable()->after('foto_ktp');
            }
            if (!Schema::hasColumn('drivers', 'foto_sim')) {
                $table->string('foto_sim')->nullable()->after('foto_kk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'foto_sim')) {
                $table->dropColumn('foto_sim');
            }
            if (Schema::hasColumn('drivers', 'foto_kk')) {
                $table->dropColumn('foto_kk');
            }
            if (Schema::hasColumn('drivers', 'foto_ktp')) {
                $table->dropColumn('foto_ktp');
            }
        });
    }
};
