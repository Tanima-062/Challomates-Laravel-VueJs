<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SalesPartner;
use App\Models\Contract;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingFee>
 */
class BoosterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // $type = $this->faker->randomElement(['One Time', 'Recurring']);
        // $type = $this->faker->randomElement(['One Time']);
        $contract = Contract::inRandomOrder()->first();
        // $posting_time = null;
        // $start = null;

        $posting_time = $this->faker->dateTimeBetween('now', '+2 month');
        $start = $posting_time->format('Y-m-d');
        $end = null;

        return [
            'sales_partner_id' => $contract->sales_partner_id,
            'contract_id' => $contract->id,
            'title' => $this->faker->name(),
            'file' => null,
            'file_name' => null,
            'range' => $this->faker->randomFloat(2, 10, 30),
            'type' => 'One Time',
            'posting_time' => $posting_time,
            'start' => $start,
            'end' => $end,
            'status' =>  $this->faker->randomElement(['active','inactive'])
        ];
    }
}
