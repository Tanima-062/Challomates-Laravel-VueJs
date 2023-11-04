<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoriesMedia>
 */
class StoryMediaFactory extends Factory
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
            'media_path'    =>  $this->faker->imageUrl(200,150),
            'media_type'    =>  'image'
        ];
    }
}
