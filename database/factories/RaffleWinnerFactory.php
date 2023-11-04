<?php

namespace Database\Factories;

use App\Models\MobileAppUser;
use App\Models\Participation;
use App\Models\Raffle;
use App\Models\RaffleWinner;
use App\Models\Sweepstake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RaffleWinner>
 */
class RaffleWinnerFactory extends Factory
{
    public array $raffle;
    public array $sweepstake;
    public array $participation;
    public ConsoleOutput $consoleOutput;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);

        /*$this->consoleOutput = new ConsoleOutput();
        $this->consoleOutput->writeln('sweepstakeIdArr -> ' . $sweepstakeArrStr);*/


    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['raffle_id' => "mixed", 'mobile_app_user_id' => "mixed"])] public function definition(): array
    {


        return array();
    }
}
