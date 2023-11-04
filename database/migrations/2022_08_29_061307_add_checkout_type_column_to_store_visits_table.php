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
            $table->enum('checkout_type', ['automatic', 'manual'])->nullable()->after('receipt');
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
            $table->dropColumn('checkout_type');
        });
    }
};
