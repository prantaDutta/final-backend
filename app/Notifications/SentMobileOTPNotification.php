<?php

namespace App\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class SentMobileOTPNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct(
        public int $otp,
    )
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        return ['database' /*, 'nexmo' */];
    }

    /**
     * Saving Data to the Database
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase(mixed $notifiable): array
    {
        return [
            'msg' => 'You Will receive an OTP in a minute'
        ];
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     *
     * @return NexmoMessage|array
     */
    public function toNexmo(): NexmoMessage|array
    {
        // Uncomment this line to send message
//        return (new NexmoMessage)
//            ->content('Your Grayscale OTP is '.$this->otp)
//            ->from('Grayscale');
        return [];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            //
        ];
    }
}
