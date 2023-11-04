<?php

namespace App\Notifications\MobileAppUser;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MobileAppUserRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/mobile-app-user/registration-complete', [
            'email'  =>  $notifiable->email,
            'token'     =>  $this->token
        ]);

        $otp = $notifiable->verification_token;

        $name = $notifiable->first_name . " " . $notifiable->last_name;
        return (new MailMessage)
            ->subject('CHalloMates Registrierung')
            ->view('mail.mobile-app-users.self-registration', ['url'=> $url, 'otp'=>$otp, 'name'=>$name])
        ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
