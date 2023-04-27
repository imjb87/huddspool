<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InviteNotification extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via ($notifiable) {
        return ['mail'];
    }    

    public function toMail($notifiable)
    {
        $url = route('invite.register', $this->token);

        return (new MailMessage)
            ->line('You have been invited to create an account.')
            ->action('Set your password', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
