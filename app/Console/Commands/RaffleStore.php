<?php

namespace App\Console\Commands;

use App\Models\Raffle;
use App\Models\Sweepstake;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RaffleStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find sweepstake which are available for raffle draw.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $totalPopulated = 0;
        $sweepstakes = Sweepstake::query()
            ->leftJoin( 'raffles', 'raffles.sweepstake_id', '=', 'sweepstakes.id' )
            ->where( 'sweepstakes.publish_status', 'published' )
            ->where( 'sweepstakes.raffle_time', '<', now()->format( 'Y-m-d H:i:s' ) )
            ->whereNull( 'sweepstakes.status' )
            ->whereNull( 'raffles.id' )
            ->get(
                array(
                    'sweepstakes.id as sweepstake_id'
                )
            );

        $totalSweepstakes = $sweepstakes->count();

        try {
            foreach ( $sweepstakes as $sweepstake ) {
                Raffle::create(
                    array(
                        'sweepstake_id' => $sweepstake['sweepstake_id']
                    )
                );
                $totalPopulated++;
            }

            info( "Raffles data has been populated with the number ${totalPopulated} of ${totalSweepstakes}." );
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            info( "Raffles data has been populated with the number ${totalPopulated} of ${totalSweepstakes} with the error message \"${$message}\"." );
        }
    }
}
