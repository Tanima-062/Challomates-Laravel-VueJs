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
        Schema::create('sales_partner_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->time('first_start_time');
            $table->time('first_end_time');
            $table->time('last_start_time')->nullable();
            $table->time('last_end_time')->nullable();
            $table->unsignedBigInteger('sales_partner_id');
            $table->timestamps();

            $table->foreign('sales_partner_id')->references('id')->on('sales_partners');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_partner_opening_hours');
    }
};
