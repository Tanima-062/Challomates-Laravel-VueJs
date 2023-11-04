<?php

namespace Database\Factories;

use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use App\Models\StoreVisits;
use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stories>
 */
class StoryFactory extends Factory
{

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Story $story) {
            for($i = 0;  $i < mt_rand(1, 10); $i++){
                $story->media()->create([
                    'media_path'        =>  $this->faker->imageUrl(200, 150),
                    'media_type'        =>  'image'
                ]);
            }

            $mobileAppUsers = MobileAppUser::where('id', '!=', $story->mobile_app_user_id)->limit(mt_rand(1,5))->get()->pluck('id')->toArray();
            $story->tagged()
                ->syncWithPivotValues($mobileAppUsers, ['creator_id'=>$story->mobile_app_user_id]);
            ;

            // $story->storyUsers()->sync([$story->mobile_app_user_id, ...$mobileAppUsers]);
        });
    }


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $sales_partner =   SalesPartner::inRandomOrder()->first();
        $mobile_app_user =   $mobile_app_user =  MobileAppUser::inRandomOrder()->first();

        return [
            'sales_partner_id'          =>  $sales_partner->id,
            'mobile_app_user_id'        =>  $mobile_app_user->id,
            'check_in_id'               =>  StoreVisits::factory()->create([
                'sales_partner_id'          =>  $sales_partner->id,
                'mobile_app_user_id'        => $mobile_app_user->id,
            ])->id,
            'title'                     =>  $this->faker->text(mt_rand(100, 150))
        ];
    }
}
