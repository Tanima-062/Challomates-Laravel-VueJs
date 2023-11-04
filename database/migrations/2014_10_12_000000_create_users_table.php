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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 30)->index('first_name');
            $table->string('last_name', 30)->index('last_name');
            $table->string('avatar')->nullable();
            $table->enum('user_medium', ['email', 'phone'])->default('email');
            $table->string('email')->unique()->nullable();
            $table->string('phone_number')->nullable();
            $table->string('country_iso_code', 10)->nullable();
            $table->string('prefix_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->unsignedBigInteger('language_id')->default(2);

            $table->enum('type', ['system_admin', 'challo_mates_admin', 'company_consultant', 'sales_partners', 'mobile_app_user']);
            $table->string('verification_token')->nullable();
            $table->enum('status', ['new', 'active', 'inactive', 'pending', 'request'])->default('new');
            $table->string('fcm_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('language_id')->references('id')->on('language');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
