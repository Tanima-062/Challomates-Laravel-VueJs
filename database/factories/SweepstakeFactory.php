<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SweepstakeFactory extends Factory
{
    private $challao_mates_admin;
    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);
        $this->challao_mates_admin = User::where( 'type', 'challo_mates_admin' )->pluck('id')->toArray();
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // $runtime_from = $now->addDays(mt_rand(2, 5));
        $runtime_from = Carbon::now()->subDays(mt_rand(5, 15));
        $runtime_to =  Carbon::now()->addDays(mt_rand(6, 25));
        //$challao_mates_admin = User::factory()->create(['type'=>'challo_mates_admin']);
        $status = $this->faker->randomElement(['canceled', 'drawn', null]);

        $total_sweepstake_number_position = mt_rand(3, 7);
        $winning_number_position_from = mt_rand(1, $total_sweepstake_number_position);
        $winning_number_position_to = mt_rand($winning_number_position_from, $total_sweepstake_number_position);
        $number_of_winners = ( ( $winning_number_position_to - $winning_number_position_from ) + 1 );

        return [
            //'challomates_admin_id'  =>  $challao_mates_admin->id,
            'challomates_admin_id'  =>  $this->faker->randomElement( $this->challao_mates_admin ),
            'name'      =>  $this->faker->text(25),
            'type'      =>  'sweepstake',//$this->faker->randomElement(['sweepstake', 'jackpot']),
            'runtime_from'  =>  $runtime_from,
            'runtime_to'    => $runtime_to,
            'raffle_time'   =>  $runtime_to->addDays(5),
            'price'         =>  $this->faker->numberBetween(50, 100),
            'number_of_winners' =>  $number_of_winners,
            'total_sweepstake_number_position'  =>  $total_sweepstake_number_position,
            'winning_number_position_from'  =>  $winning_number_position_from,
            'winning_number_position_to'    =>   $winning_number_position_to,
            'number_of_coins_for_participation' =>  $this->faker->numberBetween(50, 200),
            'status'    =>  $status,
            'publish_status'    =>  ($status == 'drawn') ? 'published' : $this->faker->randomElement(['published', 'not_published'])
        ];
    }
}
