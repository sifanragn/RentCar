<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Google
        $table->string('google_id')->nullable();
        $table->string('google_name')->nullable();
        $table->string('google_email')->nullable();
        $table->string('google_avatar')->nullable();
        $table->text('google_token')->nullable();

        // LinkedIn
        $table->string('linkedin_id')->nullable();
        $table->string('linkedin_name')->nullable();
        $table->string('linkedin_email')->nullable();
        $table->text('linkedin_token')->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'google_id','google_name','google_email',
            'google_avatar','google_token',
            'linkedin_id','linkedin_name','linkedin_email',
            'linkedin_token'
        ]);
    });
}

};
