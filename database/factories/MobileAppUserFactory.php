<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\SalesPartner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MobileAppUser>
 */
class MobileAppUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // $lang = 46.818188;
        // $long = 8.227512;

        // $latitude = $this->faker->latitude($min = ($lang - (rand(0, 50) / 1000)), $max = ($lang + (rand(0, 50) / 1000)));
        // $longitude = $this->faker->longitude($min = ($long - (rand(0, 50) / 1000)), $max = ($long + (rand(0, 50) / 1000)));
        $type = $this->faker->randomElement(['direct_consumer', 'distribution_consumer']);
        $sales_partner_id = $type == 'distribution_consumer' ? SalesPartner::inRandomOrder()->first()->id : $this->faker->randomElement([SalesPartner::inRandomOrder()->first()->id, null]);

        $country_iso_code = 'CH';
        return [
            'photo'             =>  $this->faker->imageUrl(300, 300),
            'username'          =>  uniqid(),
            'first_name'        =>  $this->faker->firstName(),
            'last_name'         =>  $this->faker->lastName(),
            'date_of_birth'     =>  $this->faker->date(),
            'email'             =>  $this->faker->email(),
            'street'            =>  $this->faker->streetName(),
            'house_number'      =>  $this->faker->streetSuffix(),
            'zip_code'          =>  $this->faker->postcode(),
            'city'              =>  $this->faker->city,
            // 'country'           =>  $this->faker->country(),
            'country'           =>  $this->faker->countryCode(),
            'country_iso_code'  =>  $country_iso_code,
            'phone_number'      =>  str_replace('+', "", $this->faker->e164PhoneNumber($country_iso_code)),
            'type'              =>  $type,
            'sales_partner_id'  =>  $sales_partner_id,
            'privacy'           =>  $this->faker->boolean(),
            'password'          =>  bcrypt('password'), //password
            'email_verified_at' =>  now(),
            'language_id'       =>  Language::inRandomOrder()->first()->id,
            'status'            =>  $this->faker->randomElement(['new', 'active', 'inactive', 'pending',]),
            'created_at'        => $this->faker->dateTimeBetween('-1 month')
        ];
    }
}
