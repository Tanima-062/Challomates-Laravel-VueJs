<?php

namespace App\Console;

use App\Jobs\ChartDataSnapshotJob;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('telescope:prune')->daily();


        $schedule->call(function () {

            $files = Storage::disk(env('FILESYSTEM_DISK', 'public'))->files('temp');
            $directories = Storage::disk(env('FILESYSTEM_DISK', 'public'))->directories('temp');

            foreach ($directories as $key => $path) {
                if (Carbon::parse(Storage::disk(env('FILESYSTEM_DISK', 'public'))->lastModified($path))->lessThan(Carbon::now()->subDay())) {
                    Storage::disk(env('FILESYSTEM_DISK', 'public'))->deleteDirectory($path);
                }
            }

            foreach ($files as $key => $file) {
                if (Carbon::parse(Storage::disk(env('FILESYSTEM_DISK', 'public'))->lastModified($file))->lessThan(Carbon::now()->subDay())) {
                    Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($file);
                }
            }
        })->everyMinute();

        $schedule->job(new ChartDataSnapshotJob)->daily();

        $schedule->command('booster-post:store')->everyMinute()->runInBackground();

        $schedule->command('telescope:prune')->daily();

        $schedule->command('checkin-notifications:send')->everyMinute()->runInBackground();

        $schedule->command('raffle:store')->everyMinute()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
