<?php

use App\Models\MobileAppUser;
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
        Schema::create('daily_users_count', function (Blueprint $table) {
            $table->id();
            $table->integer('mobile_app_users_count')->default(0);
            $table->unsignedBigInteger('sales_partner_id')->nullable()->index('sales_partner_id_index');
            $table->unsignedBigInteger('consultant_id')->nullable()->index('consultant_id_index');
            $table->enum('type', [MobileAppUser::DIRECT_CONSUMER, MobileAppUser::DISTRIBUTION_CONSUMER])->index('user_type');
            $table->date('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_users_count');
    }
};
