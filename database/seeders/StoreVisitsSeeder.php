<?php

namespace Database\Seeders;

use App\Models\StoreVisits;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreVisitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StoreVisits::factory(200)->create();

        for ($i = 0; $i < 30; $i++) {
            $random_date = Carbon::today()->subDays($i);
            StoreVisits::factory(20)->addFees()->create([
                'check_in_time'     => $random_date,
                'check_out_time'    =>  $random_date->addHour(),
                'created_at'        => $random_date,
            ]);
        }
    }
}
