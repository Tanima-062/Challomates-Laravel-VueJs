<?php

namespace Database\Factories;

use App\Models\MobileAppUser;
use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'story_id'      =>  Story::inRandomOrder()->first()->id,
            'mobile_app_user_id'    => MobileAppUser::active()->inRandomOrder()->first()->id
        ];
    }
}
