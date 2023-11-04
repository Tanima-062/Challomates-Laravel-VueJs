<?php

use App\Models\MobileAppUser;
use App\Models\Participation;
use App\Models\Raffle;
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
        Schema::create('raffle_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Participation::class)->nullable();
            $table->foreignIdFor(Raffle::class)->nullable();
            $table->foreignIdFor(MobileAppUser::class)->nullable();
            $table->string( 'winning_number', 20 )->nullable();
            $table->string( 'ref_winning_number', 20 )->nullable();
            $table->tinyInteger( 'winning_position' )->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('raffle_winners');
    }
};
