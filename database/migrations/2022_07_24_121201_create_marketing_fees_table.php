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
        Schema::create('marketing_fees', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_id')->nullable();
            $table->string('designation');
            $table->double('marketing_fee' ,10,2);
            $table->double('direct_consumers_senior_partner_share', 10,2);
            $table->double('direct_consumers_share_fee_challomates', 10,2);
            $table->double('direct_consumers_share_jackpot', 10,2);
            $table->double('direct_consumers_share_challomates_marketing_ag', 10,2);
            $table->double('distribution_consumers_share_of_consultants', 10,2);
            $table->double('distribution_consumers_proportion_of_sales_partners', 10,2);
            $table->double('distribution_consumers_share_jackpot', 10,2);
            $table->double('distribution_consumers_share_challomates_marketing_ag', 10,2);
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('marketing_fees');
    }
};
