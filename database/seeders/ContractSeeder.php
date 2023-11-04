<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\SalesPartner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contract::factory(20)->create();
        Contract::factory(15)->expired()->create();
    }
}
