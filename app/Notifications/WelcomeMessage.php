<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeMessage extends Notification
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
        if ($notifiable->role === "lender") {
            $optionalMsg = 'Lend and Earn Now';
        } else {
            $optionalMsg = 'Borrow Money Today';
        }
        return (new MailMessage)
            ->subject('Welcome to Grayscale. ' . $optionalMsg)
            ->greeting('Hello ' . $notifiable->name)
            ->line('GrayScale is one of the fastest growing peer to peer (P2P) lending
            platforms in Bangladesh. It connects investors or lenders looking
            for high returns with creditworthy borrowers looking for short term
            personal loans.')
            ->action('Let\'s Start', url(config('app.frontEndUrl')))
            ->line('Verify Your Account and Start Today');
    }

    # Saving data to the database
    public function toDatabase($notifiable)
    {
        if ($notifiable->role === "lender") {
            $optionalMsg = 'Lend and Earn Now';
        } else {
            $optionalMsg = 'Borrow Money Today';
        }
        return [
            'msg' => 'Welcome to Grayscale. ' . $optionalMsg
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
