<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MobileAppUserForgotPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $username;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($username, $token)
    {
        $this->username = $username;
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
        $name = $notifiable->first_name. ' '.$notifiable->last_name;
        return (new MailMessage)
            ->subject('CHalloMates Passwort zurücksetzen')
            ->view('mail.mobile-app-users.forgot-password', ['otp' => $this->token, 'name'=>$name])
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
