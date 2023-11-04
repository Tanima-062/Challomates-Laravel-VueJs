<?php

namespace Database\Factories;

use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Follower>
 */
class FollowerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $status =$this->faker->randomElement(['pending', 'accept',]);
        $mobileAppUser =  MobileAppUser::inRandomOrder()->first();

        $followable_type = $this->faker->randomElement([MobileAppUser::class, SalesPartner::class]);

        if($followable_type == MobileAppUser::class) {
            $followable_id = MobileAppUser::where('id', '!=', $mobileAppUser->id)->inRandomOrder()->first()->id;
        }else {
            $followable_id = SalesPartner::inRandomOrder()->first()->id;
            $status = 'accept';
        }

        return [
            'followable_id' =>  $followable_id,
            'followable_type' =>  $followable_type,
            'follower_id' =>  $mobileAppUser->id,
            'follower_type' =>  MobileAppUser::class,
            'status'        =>  $status,
            'accept_at'        =>  $status == 'accept' ? now() : null,
        ];
    }
}
