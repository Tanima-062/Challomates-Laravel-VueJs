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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id');
            $table->string('name')->index('name');
            $table->date('contract_term_from');
            $table->date('contract_term_to');
            $table->enum('status', ['active', 'new', 'canceled', 'expired']);
            $table->foreignId('package_id');
            $table->foreignId('marketing_fee_id');
            $table->foreignId('sales_partner_id');
            $table->timestamps();

            $table->foreign('sales_partner_id')->references('id')->on('sales_partners');
            $table->foreign('package_id')->references('id')->on('package');
            $table->foreign('marketing_fee_id')->references('id')->on('marketing_fees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};
