<?php

namespace Database\Seeders;

use App\Models\Sweepstake;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SweepStakesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        Sweepstake::truncate();
        DB::statement("SET foreign_key_checks=1");

        Sweepstake::factory()->count(100)->create();
    }
}
