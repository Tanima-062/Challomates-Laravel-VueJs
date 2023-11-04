<?php

namespace App\Notifications\ChalloMatesAdmin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $token;

    /**
     * Create a new notification instance.
     *
     * @param $token
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
        $url = url(route('register', ['token' => $this->token, 'username' => $notifiable->email]));

        $name = $notifiable->first_name . " " . $notifiable->last_name;

        return (new MailMessage)
            ->subject( trans( 'CHalloMates Einladung', [], 'de' ) )
            ->view('mail.auth.register-challomates-admin', ['name' => $name, 'url' => $url, 'language_code' => 'de']);
    }
}
