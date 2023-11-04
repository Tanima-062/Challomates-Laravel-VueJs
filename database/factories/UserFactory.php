<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $country_iso_code = 'CH';
        $user_type = $this->faker->randomElement(['challo_mates_admin', 'mobile_app_user', 'sales_partners']);

        return [
            'country_iso_code'  =>  $country_iso_code,
            'phone_number' => str_replace('+', "", $this->faker->e164PhoneNumber($country_iso_code)),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'language_id' => Language::inRandomOrder()->first()->id,
            'avatar' => $this->faker->imageUrl(),
            'email' => $this->faker->unique()->email(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'type' => $user_type,
            'status'    =>  $this->faker->randomElement([User::STATUS_NEW, User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_PENDING])
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
