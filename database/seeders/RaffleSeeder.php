<?php

namespace Database\Seeders;

use App\Models\Raffle;
use App\Models\Sweepstake;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaffleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '-1');
        DB::statement("SET foreign_key_checks=0");
        Raffle::truncate();
        DB::statement("SET foreign_key_checks=1");

        Raffle::factory(Sweepstake::where( 'publish_status', 'published' )->count())->create();
    }
}
