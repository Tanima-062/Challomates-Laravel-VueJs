<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendQrCodeEmail extends Mailable  implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $file_name;
    public $original_name;

    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sales_partner, $file_name)
    {
        $this->name =  $sales_partner->contact_person_first_name.' '.$sales_partner->contact_person_last_name;

        $this->original_name = $file_name;
        if (config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale') {
            $this->file_name = Storage::disk(env('FILESYSTEM_DISK', 'public'))->publicUrl($file_name);
        } else {
            $this->file_name = Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($file_name);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.qr-code')
            ->subject('Ihr CHalloMates QR Code')
        ->attach($this->file_name, [
            'as' => 'qr-code.png',
            'mime' => 'image/png',
        ])
        // ->attachData(base64_decode($this->data), 'qr-code.png', [
        //     'mime'      =>  'image/png'
        // ])
        ;
    }
}
