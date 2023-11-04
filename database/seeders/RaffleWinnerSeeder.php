<?php

namespace Database\Seeders;

use App\Models\RaffleWinner;
use App\Models\Sweepstake;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class RaffleWinnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '-1');
        DB::statement("SET foreign_key_checks=0");
        RaffleWinner::truncate();
        DB::statement("SET foreign_key_checks=1");

        $consoleOutput = new ConsoleOutput();

        //var_dump($this->raffleWinnerQuery()->toSql());

        foreach ( $this->raffleWinnerQuery()->get()->toArray() as $item ) {
            $item = (array) $item;
            $realRaffleWinningNumber = [];
            $winningNumberArr = explode( ',', $item['winning_number'] );
            $raffleWinningNumber = explode( '-', $item['raffle_winning_number'] );

            //$consoleOutput->writeln( $raffleWinningNumber );
            //$consoleOutput->writeln( 'from -> ' . $item['winning_number_position_from'] );
            //$consoleOutput->writeln( 'to -> ' . $item['winning_number_position_to'] );

            for ( $init = ( $item['winning_number_position_from'] - 1 ); $init < $item['winning_number_position_to']; $init++ ) {
                $realRaffleWinningNumber[] = $raffleWinningNumber[$init];
            }

            foreach ( $winningNumberArr as $winningNumber ) {
                $realNumberPositionArr = [];
                $numberPositionArr = explode( '-', $winningNumber );

                for ( $init = ( $item['winning_number_position_from'] - 1 ); $init < $item['winning_number_position_to']; $init++ ) {
                    $realNumberPositionArr[] = $numberPositionArr[$init];
                }

                $winningPositionCount = 0;
                foreach ( $realNumberPositionArr as $index => $value ) {
                    $winningPositionCount += ( $value == $realRaffleWinningNumber[$index] ) ? 1 : 0;
                }

                if ( $winningPositionCount > ( count( $realRaffleWinningNumber ) - $item['number_of_winners'] ) ) {
                    RaffleWinner::factory()->create(
                        array(
                            'participation_id' => $item['participation_id'],
                            'raffle_id' => $item['raffle_id'],
                            'mobile_app_user_id' => $item['participant_id'],
                            'ref_winning_number' => $item['raffle_winning_number'],
                            'winning_number' => $winningNumber,
                            'winning_position' => $winningPositionCount,
                        )
                    );
                }
            }
        }
    }

    private function raffleWinnerQuery(): \Illuminate\Database\Query\Builder
    {
        $query = Sweepstake::query()
            ->select(
                'sweepstakes.number_of_winners AS number_of_winners',
                'sweepstakes.winning_number_position_from AS winning_number_position_from',
                'sweepstakes.winning_number_position_to AS winning_number_position_to',
                'p.id AS participation_id',
                'p.winning_number AS winning_number',
                'p.mobile_app_user_id AS participant_id',
                'r.id AS raffle_id',
                'r.winning_number AS raffle_winning_number',
            )
            ->leftJoin( 'participations AS p', 'p.sweepstake_id', '=', 'sweepstakes.id' )
            ->leftJoin( 'raffles AS r', 'r.sweepstake_id', '=', 'sweepstakes.id' );

        return DB::table( DB::raw( "({$query->toSql()}) as table1" ) )
            ->whereNotNull( 'raffle_winning_number' )
            ->whereNotNull( 'participation_id' );
    }
}
