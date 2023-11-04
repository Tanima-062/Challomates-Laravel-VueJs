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
        Schema::create('followers', function (Blueprint $table) {
            $table->id();

            //Followable
            $table->integer('followable_id');
            $table->string('followable_type');

            $table->integer('follower_id');
            $table->string('follower_type');

            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending');
            $table->timestamp('accept_at')->nullable();
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
        Schema::dropIfExists('followers');
    }
};
