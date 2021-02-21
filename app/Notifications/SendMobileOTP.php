<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class SendMobileOTP extends Notification
{
    use Queueable;
    protected $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->otp = mt_rand(100000, 999999);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'nexmo'];
    }

    /**
     * Saving Data to the Database
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $user = User::where('email', $notifiable->email)->first();
        $user->util()->update([
            'mobile_no_verify_otp' => $this->otp
        ]);
        return [
            'msg' => 'You Will receive an OTP in a minute'
        ];
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     *
     * @param mixed $notifiable
//     * @return NexmoMessage
     * @return array
     */
    public function toNexmo($notifiable)
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
