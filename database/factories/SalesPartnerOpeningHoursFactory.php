<?php

namespace Database\Factories;

use App\Models\SalesPartner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesPartnerOpeningHours>
 */
class SalesPartnerOpeningHoursFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "first_start_time" => $this->faker->dateTimeBetween("12:00", "15:00")->format("H:m"),
            "first_end_time" => $this->faker->dateTimeBetween("17:00", "23:59")->format("H:m"),
            "last_start_time" => $this->faker->dateTimeBetween("01:00", "02:59")->format("H:m"),
            "last_end_time" => $this->faker->dateTimeBetween("06:00", "11:00")->format("H:m"),
        ];
    }
}
