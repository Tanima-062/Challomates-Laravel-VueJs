<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\BranchCategory;
use App\Models\Contract;
use App\Models\SalesPartnerOpeningHours;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesPartners>
 */
class SalesPartnerFactory extends Factory
{

    /**
     * Here We will paerform any side effects
     *
     * @return void
     */
    public function configure()
    {
        $days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        return $this->afterCreating(function ($sales_partner) use ($days) {

            if ($sales_partner->no_information == false) {
                $days = $this->faker->randomElements($days, 5);
                foreach ($days as $key => $day) {
                    SalesPartnerOpeningHours::factory(1)->create([
                        'day' => $day,
                        'sales_partner_id' => $sales_partner->id,
                    ]);
                }
            }

            Contract::factory()->create([
                'sales_partner_id'  =>  $sales_partner->id,
            ]);
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        $phone_number = $this->faker->e164PhoneNumber();
        $branch_id =  Branch::inRandomOrder()->first()->id;
        $branch_category_id = BranchCategory::where('branch_id', $branch_id)->inRandomOrder()->first()->id;
        // $latitude = $this->faker->latitude();
        // $longitude = $this->faker->longitude();

        $lang = 46.818188;
        $long = 8.227512;

        $latitude = $this->faker->latitude($min = ($lang - (rand(0, 50) / 1000)), $max = ($lang + (rand(0, 50) / 1000)));
        $longitude = $this->faker->longitude($min = ($long - (rand(0, 50) / 1000)), $max = ($long + (rand(0, 50) / 1000)));

        return [
            "company_name" => $this->faker->company,
            "profile_picture" => $this->faker->imageUrl(),
            "receipt_template" => $this->faker->imageUrl(),
            "receipt_template_name" => $this->faker->name(),
            "status" => $this->faker->randomElement(["new", "active", "inactive"]),
            "website" => $this->faker->url,
            "street" => $this->faker->text(20),
            "house_number" => "hose, ses" . $this->faker->randomDigitNotNull(),
            "zip_code" => $this->faker->postcode,
            "city" => $this->faker->city,
            "country" => $this->faker->countryCode(),
            "no_information" => false,
            "contact_person_first_name" => $this->faker->firstName,
            "contact_person_last_name" => $this->faker->lastName,
            "contact_person_email" => $this->faker->safeEmail,
            "contact_person_country_iso_code" => $this->faker->countryISOAlpha3,
            "contact_person_phone_number" => $phone_number,
            "contact_person_full_phone_number" =>  $phone_number,
            "branch_id" => $branch_id,
            "branch_category_id" => $branch_category_id,
            "consultant_id" => User::where('type', 'company_consultant')->where('status', 'active')->inRandomOrder()->first()->id,
            "challo_mates_admin_id" => User::where('type', 'challo_mates_admin')->where('status', 'active')->inRandomOrder()->first()->id,
            "map_address" => $this->faker->address(),
            "coordinates" => new Point($latitude, $longitude),
        ];
    }
}
