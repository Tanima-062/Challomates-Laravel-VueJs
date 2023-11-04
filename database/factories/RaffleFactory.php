<?php

namespace Database\Factories;

use App\Models\MobileAppUser;
use App\Models\Raffle;
use App\Models\Sweepstake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Raffle>
 */
class RaffleFactory extends Factory
{
    public array $sweepstake;
    public array $sweepstake_number_position;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);
        $sweepstakes = Sweepstake::where('publish_status', 'published');
        $this->sweepstake = $sweepstakes->pluck('id')->toArray();
        $this->sweepstake_number_position = $sweepstakes->pluck('total_sweepstake_number_position', 'id')->toArray();
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape(['sweepstake_id' => "mixed", 'winning_number' => "mixed", 'video_src_path' => "mixed", 'started_at' => "\DateTime", 'stopped_at' => "\DateTime"])] public function definition(): array
    {
        $video_src_path = $this->faker->randomElement( array('123.webm', '456.mp4', '789.mp4', 'mov_bbb.mp4', null));
        $sweepstake_id = $this->faker->randomElement( $this->sweepstake );
        $sweepstake_id_index = array_search( $sweepstake_id, $this->sweepstake );
        array_splice($this->sweepstake, $sweepstake_id_index, 1);

        return array(
            'sweepstake_id' => $sweepstake_id,
            'winning_number' => ($video_src_path !== null ) ? $this->generateWinningNumber($sweepstake_id) : null,
            'video_src_path' => $video_src_path,
            'started_at' => ($video_src_path == null) ? $video_src_path : $this->faker->dateTime(),
            'stopped_at' => ($video_src_path == null) ? $video_src_path : $this->faker->dateTime(),
        );
    }

    private function generateWinningNumber($sweepstake_id): string
    {
        $win_num = array();
        for ($i = 0; $i < $this->sweepstake_number_position[$sweepstake_id]; $i++) {
            $win_num[] = rand(0, 9);
        }

        return join( '-', $win_num );
    }
}
