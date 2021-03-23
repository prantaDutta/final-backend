<?php

namespace App\Notifications;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class SendMobileOTP extends Notification implements ShouldQueue
{
    use Queueable;

    protected int $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     * @throws Exception
     */
    public function __construct()
    {
        $this->otp = random_int(100000, 999999);
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
    #[ArrayShape(['msg' => "string"])] public function toDatabase(mixed $notifiable): array
    {
        $user = User::where('email', $notifiable->email)->first();
        if ($user === null) {
            throw new ModelNotFoundException();
        }
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
