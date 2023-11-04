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
        Schema::create('sweepstakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challomates_admin_id')->constrained('users');
            $table->string('prefix_id')->nullable();
            $table->string('name', 30);
            $table->enum('type', ['sweepstake', 'jackpot']);
            $table->dateTime('runtime_from');
            $table->dateTime('runtime_to');
            $table->dateTime('raffle_time')->nullable();
            $table->string('price')->nullable();
            $table->integer('number_of_winners')->nullable();
            $table->integer('total_sweepstake_number_position');
            $table->integer('winning_number_position_from');
            $table->integer('winning_number_position_to');
            $table->decimal('number_of_coins_for_participation');
            $table->enum('status', ['canceled', 'drawn', null])->nullable()->default(null);
            $table->enum('publish_status', ['published', 'not_published'])->default('not_published');
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
        Schema::dropIfExists('sweepstakes');
    }
};
