<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobile_app_user_id')->constrained('mobile_app_users');
            $table->foreignId('blocked_id')->constrained('mobile_app_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_user');
    }
};
