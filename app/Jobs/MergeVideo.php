<?php

namespace App\Jobs;

use App\Models\Raffle;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class MergeVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 300000;

    // public $tries = 10;
    public $tries = 1;

    public $failOnTimeout = false;





    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Raffle $raffle, public string $sourceDirectory, public string $save_path, public string $disk = 'public', public bool $deleteAfterCompletation = true)
    {
        // $this->onQueue('video_processing');
        info('testign', [$sourceDirectory, $disk, $deleteAfterCompletation]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {



        // $name = substr($this->destianation, 0, strrpos($this->destianation, '.'));
        // $extension = substr_replace($this->destianation, '', 0, strrpos($this->destianation, '.'));

        $files = Storage::disk($this->disk)->files($this->sourceDirectory);
        if (count($files)) {

            $extension = pathinfo($files[0], PATHINFO_EXTENSION);
            $file_name = sprintf("%s.%s", uniqid(), $extension);

            $contact_video = "raffle/$file_name";
            info('concat strtinng ' . $contact_video);
            FFMpeg::fromDisk($this->disk)->open($files)->export()
                ->concatWithoutTranscoding()
                ->save($contact_video);

            info('contact complete ', [$file_name, $extension]);

            if ($extension !== 'webm') {
                info('starting conversion save path === ' . $this->save_path);

                FFMpeg::fromDisk($this->disk)
                    ->open($contact_video)
                    ->export()
                    ->inFormat(new \FFMpeg\Format\Video\WebM())
                    ->save($this->save_path);

                info('convert complete');

                Storage::disk($this->disk)->delete($contact_video);
                info('delete compelte');
            } else {
                Storage::disk($this->disk)->move($contact_video, $this->save_path);
            }



            if ($this->deleteAfterCompletation) {
                info('starting conversion save path === ' . $this->save_path);
                Storage::disk($this->disk)->deleteDirectory($this->sourceDirectory);
                info('temp file delete compelte');
            }
        }
    }


    // public function retryAfter(){
    //     return now()->addMinutes(($this->attempts() * $this->tries) + 2);
    // }
}
