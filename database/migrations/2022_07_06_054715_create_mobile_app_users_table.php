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
        Schema::create('mobile_app_users', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id')->nullable();
            $table->string('photo')->nullable();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('email')->unique();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('country_iso_code', 10)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('full_phone_number')->nullable();
            $table->enum('type', ['direct_consumer', 'distribution_consumer'])->default('direct_consumer');
            $table->boolean('privacy');

            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_token')->nullable();

            $table->foreignId('language_id')->constrained('language')->default(1);
            $table->enum('status', ['new', 'active', 'inactive', 'pending', 'request'])->default('new');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_app_users');
    }
};
