<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Instagram
            $table->string('instagram_id')->nullable();
            $table->string('instagram_username')->nullable();
            $table->text('instagram_token')->nullable();

            // Facebook
            $table->string('facebook_id')->nullable();
            $table->string('facebook_name')->nullable();
            $table->text('facebook_token')->nullable();

            // TikTok
            $table->string('tiktok_id')->nullable();
            $table->string('tiktok_username')->nullable();
            $table->text('tiktok_token')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_id', 'instagram_username', 'instagram_token',
                'facebook_id', 'facebook_name', 'facebook_token',
                'tiktok_id', 'tiktok_username', 'tiktok_token',
            ]);
        });
    }
};
