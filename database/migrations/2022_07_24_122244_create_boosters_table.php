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
        Schema::create('boosters', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id')->nullable();
            $table->integer('sales_partner_id');
            $table->integer('contract_id');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('file')->nullable();
            $table->string('file_name')->nullable();
            $table->double('range');
            $table->enum('type',['One Time','Recurring'])->default('One Time');
            $table->dateTime('posting_time')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->enum('status',['active','inactive','new','expired','posted'])->default('active');
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
        Schema::dropIfExists('boosters');
    }
};
