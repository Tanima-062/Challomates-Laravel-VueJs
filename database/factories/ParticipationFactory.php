<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\MobileAppUser;
use App\Models\Participation;
use App\Models\SalesPartner;
use App\Models\Sweepstake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participation>
 */
class ParticipationFactory extends Factory
{
    // private array $mobileAppUser;
    // private array $sweepstake;

    /**
     * @var ConsoleOutput
     */
    /*private ConsoleOutput $consoleOutput;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
    }*/

    // public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    // {
    //     parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

    //     $this->mobileAppUser = MobileAppUser::all()->pluck('id')->toArray();
    //     $this->sweepstake = Sweepstake::where('publish_status', 'published')->pluck('id')->toArray();
    // }

    // /**
    //  * Define the model's default state.
    //  *
    //  * @return array<string, mixed>
    //  */
    // #[ArrayShape(['participated_at' => "\DateTime", 'winning_number' => "string", 'mobile_app_user_id' => "mixed", 'sweepstake_id' => "mixed"])] public function definition(): array
    // {
    //     $mobile_app_user_id = $this->faker->randomElement($this->mobileAppUser);
    //     $sweepstake_id = $this->faker->randomElement($this->sweepstake);

    //     $participationMaxId = (int) Participation::max('participation_id');
    //     dd($participationMaxId);
    //     return [
    //         'winning_number' => $this->generateWinningNumber(),
    //         'mobile_app_user_id' => $mobile_app_user_id,
    //         'sweepstake_id' => $sweepstake_id,
    //         'participation_id'  => $participationMaxId + 1
    //     ];
    // }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $mobileAppUser = MobileAppUser::inRandomOrder()->first();
        $sweepstake = Sweepstake::inRandomOrder()->first();
        $participationMaxId = 0;

        $participate =Participation::where('mobile_app_user_id', $mobileAppUser->id)
            ->where('sweepstake_id', $sweepstake->id)
            ->latest()
            ->first();

        if($participate){
            $participationMaxId = $participate->participation_id;
        }else {
            $participationMaxId = (int) Participation::query()
            ->max('participation_id')
            ;
        }

        //$this->consoleOutput->writeln( $participationMaxId );

        return [
            // 'participated_at' => $this->faker->dateTime(),
            'winning_number' => $this->generateWinningNumber($sweepstake),
            'mobile_app_user_id' => $mobileAppUser->id,
            'sweepstake_id' => $sweepstake->id,
            'participation_id'  =>  sprintf("%08d", $participationMaxId + 1)
        ];
    }

    private function generateWinningNumber($sweepstake): string
    {
        $win_num = array();
        for ($i = 0; $i < $sweepstake->total_sweepstake_number_position; $i++) {
            $win_num[] = rand(0, 9);
        }

        return join('-', $win_num);
    }
}

