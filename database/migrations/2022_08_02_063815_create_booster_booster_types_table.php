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
        Schema::create('booster_booster_types', function (Blueprint $table) {
            $table->id();
            $table->integer('booster_id');
            $table->integer('number_of_boosters_month')->nullable();
            $table->enum('week',['1st','2nd','3rd','4th','last'])->nullable();
            $table->enum('weekday',['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'])->nullable();
            $table->time('time')->nullable();
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
        Schema::dropIfExists('booster_booster_types');
    }
};
