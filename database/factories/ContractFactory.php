<?php

namespace Database\Factories;

use App\Models\MarketingFee;
use App\Models\Package;
use App\Models\SalesPartner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "contract_term_from" => $this->faker->dateTimeBetween('-2 month', 'now'),
            "contract_term_to" => $this->faker->dateTimeBetween('+2 month', '+4 month'),
            "sales_partner_id" => SalesPartner::inRandomOrder()->first()->id,
            "marketing_fee_id" => MarketingFee::inRandomOrder()->where('status', 'active')->first()->id,
            "package_id" => Package::inRandomOrder()->where('status', 'active')->first()->id,
            "status" => $this->faker->randomElement(['active', 'new']),
        ];
    }

    public function expired()
    {
        return $this->state(function(array $attributes) {
            return [
                'contract_term_from' => $this->faker->dateTimeBetween('-5 month', '-3 month'),
                'contract_term_to' => $this->faker->dateTimeBetween('-2 month', '-10 days'),
                'status' => 'expired'
            ];
        });
    }
}
