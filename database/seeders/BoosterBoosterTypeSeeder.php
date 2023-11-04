<?php

namespace Database\Seeders;

use App\Models\BoosterBoosterType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoosterBoosterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BoosterBoosterType::factory(100)->create();
    }
}
