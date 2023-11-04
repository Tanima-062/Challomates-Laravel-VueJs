<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {

        /*$medium = [];

        if ($notifiable->user_medium == 'email') {
            $medium = ['mail'];
        } else if ($notifiable->user_medium == 'phone') {
            $medium = [TwilioChannel::class];
        }*/

        return ['mail'];
    }


    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', ['token' => $this->token, 'username' => $notifiable->email]));

        //$locale = User::with('language')->where('id', 35)->first()->language->code;

        $name = $notifiable->first_name . " " . $notifiable->last_name;
        return (new MailMessage)
            ->subject('CHalloMates Passwort zurücksetzen')
            ->view('mail.auth.reset-password', ['name'=>$name, 'url' => $url,]);
    }


    /*public function toTwilio($notifiable)
    {
        $url = url(route('password.reset', ['token' => $this->token, 'username' => $notifiable->phone_number]));
        dump($url);
        return (new TwilioSmsMessage())
            ->content("Please use this link to reset your password.\n{$url}");
    }*/
}
