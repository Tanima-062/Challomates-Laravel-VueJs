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
        Schema::table('mobile_app_users', function (Blueprint $table) {
            $table->foreignId('sales_partner_id')->nullable()->constrained('sales_partners');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_app_users', function (Blueprint $table) {
            $table->dropForeign('mobile_app_users_sales_partner_id_foreign');
            $table->dropColumn('sales_partner_id');
        });
    }
};
