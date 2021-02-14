<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
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
        $uniq_id = uniqid('', true);
        $otp = mt_rand(100000, 999999);
        $user = User::where('email', $notifiable->email)->first();
        $user->util()->update([
            'email_verify_token' => $uniq_id,
            'email_verify_otp' => $otp
        ]);
        return (new MailMessage)
            ->subject('Verification Email')
            ->greeting('Hello Mr. ' . $notifiable->name)
            ->line('GrayScale is one of the fastest growing peer to peer (P2P) lending
            platforms in Bangladesh. It connects investors or lenders looking
            for high returns with creditworthy borrowers looking for short term
            personal loans.')
            ->line('Your One Time Password (OTP) is '. $otp)
            ->action('Verify Your Email', url('/api/verify-email/' . $uniq_id));
    }

    # Saving data to the database
    public function toDatabase($notifiable)
    {
        return [
            'msg' => 'Verification Email Sent. Check Inbox'
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
