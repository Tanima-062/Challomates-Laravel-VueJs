<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchCategory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branch_categories = [
            [
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Hotel", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Club", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Shisha-Bar", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Bar", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Restaurant", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Fastfood", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Gastro')->first()?->id ?? 1, "name" => "Cafe", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
            ],
            [
                ["branch_id" => Branch::where('name', 'Beauty')->first()?->id ?? 1, "name" => "Nails", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Beauty')->first()?->id ?? 1, "name" => "Coiffeur/Barber", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Beauty')->first()?->id ?? 1, "name" => "Kosmetik", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Beauty')->first()?->id ?? 1, "name" => "Tattoo/Piercing", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
            ],
            [
                ["branch_id" => Branch::where('name', 'Activity')->first()?->id ?? 1, "name" => "Abenteuer", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Activity')->first()?->id ?? 1, "name" => "Thermalbad/Wellness", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Activity')->first()?->id ?? 1, "name" => "Kino", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Activity')->first()?->id ?? 1, "name" => "Fitness", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
            ],
            [
                ["branch_id" => Branch::where('name', 'Shopping')->first()?->id ?? 1, "name" => "Shop", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Shopping')->first()?->id ?? 1, "name" => "Lebensmittel", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
                ["branch_id" => Branch::where('name', 'Shopping')->first()?->id ?? 1, "name" => "Tankstelle", "created_at" => Carbon::now(), "updated_at" => Carbon::now()],
            ]
        ];

        foreach ($branch_categories as $key => $value) {
            BranchCategory::insert($value);
        }
    }
}
