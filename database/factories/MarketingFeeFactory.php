<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingFee>
 */
class MarketingFeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['active','inactive']);
        return [
            'designation' => $this->faker->name(),
            'marketing_fee' => $this->faker->randomFloat(2, 10, 30),
            'direct_consumers_senior_partner_share' => $this->faker->randomFloat(2, 10, 30),
            'direct_consumers_share_fee_challomates' => $this->faker->randomFloat(2, 10, 30),
            'direct_consumers_share_challomates_marketing_ag' => $this->faker->randomFloat(2, 10, 30),
            'distribution_consumers_share_challomates_marketing_ag' => $this->faker->randomFloat(2, 10, 30),
            'distribution_consumers_share_of_consultants' => $this->faker->randomFloat(2, 10, 30),
            'direct_consumers_share_jackpot' => $this->faker->randomFloat(2, 10, 30),
            'distribution_consumers_share_jackpot' => $this->faker->randomFloat(2, 10, 30),
            'distribution_consumers_proportion_of_sales_partners' => $this->faker->randomFloat(2, 10, 30),
            'status' => $status
        ];
    }
}
