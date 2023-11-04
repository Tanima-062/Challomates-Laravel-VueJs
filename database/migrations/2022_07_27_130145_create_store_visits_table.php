<?php

use App\Models\Contract;
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
        Schema::create('store_visits', function (Blueprint $table) {
            $table->id();

            $table->string('prefix_id');
            $table->foreignId('sales_partner_id')->constrained('sales_partners');
            $table->foreignId('mobile_app_user_id')->constrained('mobile_app_users');
            $table->foreignIdFor(Contract::class)->constrained()->nullable();
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->decimal('turnover')->nullable();
            $table->decimal('posting_coins')->nullable();
            $table->decimal('jackpot_share')->nullable();
            $table->string('receipt')->nullable();
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
        Schema::dropIfExists('store_visits');
    }
};
