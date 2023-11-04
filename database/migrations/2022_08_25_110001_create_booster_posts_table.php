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
        Schema::create('booster_posts', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id')->nullable();
            $table->integer('booster_id');
            $table->integer('sales_partner_id');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('file')->nullable();
            $table->string('file_name')->nullable();
            $table->double('range');
            $table->date('posting_date');
            $table->time('posting_time')->nullable();
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
        Schema::dropIfExists('booster_posts');
    }
};
