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
    Schema::table('users', function (Blueprint $table) {
        $table->string('discord_id')->nullable();
        $table->string('discord_username')->nullable();
        $table->string('discord_global_name')->nullable();
        $table->string('discord_email')->nullable();
        $table->string('discord_avatar')->nullable();
        $table->string('discord_profile_url')->nullable();
        $table->string('discord_token')->nullable();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'discord_id',
            'discord_username',
            'discord_global_name',
            'discord_email',
            'discord_avatar',
            'discord_profile_url',
            'discord_token',
        ]);
    });
}

};
