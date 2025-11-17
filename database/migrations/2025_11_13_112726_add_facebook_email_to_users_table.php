<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'facebook_email')) {
                $table->string('facebook_email')->nullable();
            }

            if (!Schema::hasColumn('users', 'facebook_link')) {
                $table->string('facebook_link')->nullable();
            }

            if (!Schema::hasColumn('users', 'facebook_avatar')) {
                $table->string('facebook_avatar')->nullable();
            }

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'facebook_email',
                'facebook_link',
                'facebook_avatar'
            ]);

        });
    }
};
