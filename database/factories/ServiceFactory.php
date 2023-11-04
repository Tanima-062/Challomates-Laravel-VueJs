<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'service_prefix_id' => 'DL' . $this->faker->randomNumber(5, true ),
            'service_name' => $this->faker->name(),
            'status' => $this->faker->randomElement( ['active', 'inactive'] ),
        ];
    }
}
