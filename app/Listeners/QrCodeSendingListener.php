<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QrCodeSendingListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if(isset($event->data) && isset($event->data['original_name'])){

            $file_name = $event->data['original_name'];

            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($file_name);
        }
    }
}
