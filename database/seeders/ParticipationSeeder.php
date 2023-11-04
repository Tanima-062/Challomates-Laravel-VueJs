<?php

namespace Database\Seeders;

use App\Models\Participation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::statement("SET foreign_key_checks=0");
         Participation::truncate();
         DB::statement("SET foreign_key_checks=1");


        for($i=0; $i<500; $i++){
            Participation::factory(1)->create();
        }
    }
}
