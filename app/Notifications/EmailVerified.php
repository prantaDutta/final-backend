<?php

namespace App\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerified extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Email Verified')
            ->greeting('Hello Mr. ' . $notifiable->name)
            ->line('Your Email ' . $notifiable->email . ' is successfully verified')
            ->line('Thank You For Your Co-operation')
            ->action('Go to Homepage', url('/'));
    }

    # Saving data to the database
    public function toDatabase($notifiable)
    {
        $user = User::where('email', $notifiable->email)->first();
        $user->util()->update([
            'email_verified_at' => Carbon::now(),
            'email_verified' => true
        ]);
        return [
            'msg' => 'Your Email is Successfully Verified'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
