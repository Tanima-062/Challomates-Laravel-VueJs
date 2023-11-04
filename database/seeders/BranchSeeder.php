<?php

namespace Database\Seeders;

use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branches = [
            ["name" => "Gastro", "created_at" => Carbon::now(), 'updated_at' => Carbon::now()],
            ["name" =>  "Beauty", "created_at" => Carbon::now(), 'updated_at' => Carbon::now()],
            ["name" =>  "Activity", "created_at" => Carbon::now(), 'updated_at' => Carbon::now()],
            ["name" =>  "Shopping", "created_at" => Carbon::now(), 'updated_at' => Carbon::now()]
        ];
        Branch::insert($branches);
    }
}
