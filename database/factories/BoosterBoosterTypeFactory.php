<?php

namespace Database\Factories;

use App\Models\Booster;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingFee>
 */
class BoosterBoosterTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $contract = Contract::inRandomOrder()->first();
        $posting_time = null;
        $start = $this->faker->dateTimeBetween('-1 month','now')->format('Y-m-d');
        $end = $this->faker->dateTimeBetween('+1 month','+2 month')->format('Y-m-d');

        $booster = Booster::factory()->create([
            'type'  =>  'Recurring',
            'sales_partner_id' => $contract->sales_partner_id,
            'contract_id' => $contract->id,
            'title' => $this->faker->name(),
            'file' => null,
            'file_name' => null,
            'range' => $this->faker->randomFloat(2, 10, 30),
            'posting_time' => $posting_time,
            'start' => $start,
            'end' => $end,
            'status' =>  $this->faker->randomElement(['active','inactive'])
        ]);
        $number_of_boosters_month = null;
        $week = null;
        $weekday = null;
        $time = null;

        $number_of_boosters_month = 1;
        $week = $this->faker->randomElement(['1st','2nd','3rd','4th','last']);
        $weekday = $this->faker->randomElement(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']);
        $time = $this->faker->randomFloat(2, 10, 30);

        return [
            'booster_id' => $booster->id,
            'number_of_boosters_month' => $number_of_boosters_month,
            'week' => $week,
            'weekday' => $weekday,
            'time' => $time,
        ];
    }
}
