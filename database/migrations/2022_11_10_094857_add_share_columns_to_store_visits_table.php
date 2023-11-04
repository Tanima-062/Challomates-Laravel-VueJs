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
        Schema::table('store_visits', function (Blueprint $table) {
            $table->decimal('senior_partner_share')->nullable()->default(null);
            $table->decimal('challomate_share')->nullable()->default(null);
            $table->decimal('consultant_share')->nullable()->default(null);
            $table->decimal('sales_partner_share')->nullable()->default(null);
            $table->decimal('challomate_marketing_ag_share')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_visits', function (Blueprint $table) {
            //
        });
    }
};
