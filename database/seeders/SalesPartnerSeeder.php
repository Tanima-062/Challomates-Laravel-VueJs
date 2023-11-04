<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\SalesPartner;
use Illuminate\Database\Seeder;

use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SalesPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalesPartner::factory(100)->create();

        $faker = Factory::create();

        $lang = 23.772770;
        $long = 90.412404;
        for($i = 0; $i<500; $i++){
            $latitude = $faker->latitude($min = ($lang - (rand(0,50) / 1000)), $max = ($lang + (rand(0,50) / 1000)));
            $longitude = $faker->longitude($min = ($long - (rand(0,50) / 1000)), $max = ($long + (rand(0,50) / 1000)));
            SalesPartner::factory()->create([
                "coordinates" => new Point($latitude, $longitude),
            ]);
        }
    }

}
