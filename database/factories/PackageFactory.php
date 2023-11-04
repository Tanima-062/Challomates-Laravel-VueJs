<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PackageFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'package_prefix_id' => 'P' . $this->faker->randomNumber(5, true),
            'package_name' => $this->faker->name(),
            'services' => $this->faker->text(50),
            'registration_fee' => $this->faker->randomFloat(2, 0, 100),
            'first_year_fee' => $this->faker->randomFloat(2, 0, 100),
            'yearly_fee' => $this->faker->randomFloat(2, 0, 100),
            'coin_factor' => $this->faker->numberBetween(1, 10),
            'consulting' => $this->faker->text(40),
            'booster' => $this->faker->numberBetween(1, 10),
            'number_of_registration' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
