<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class SendVerifyEmailOTP extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $name, $email, $otp, $uniq_id;

    /**
     * Create a new notification instance.
     *
     * @param $name
     * @param $email
     * @param $otp
     * @param $uniq_id
     */
    public function __construct($name, $email, $otp, $uniq_id)
    {
        $this->name = $name;
        $this->email = $email;
        $this->otp = $otp;
        $this->uniq_id = $uniq_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Verification Email')
            ->greeting('Hello ' . $this->name)
            ->line('GrayScale is one of the fastest growing peer to peer (P2P) lending
            platforms in Bangladesh. It connects investors or lenders looking
            for high returns with creditworthy borrowers looking for short term
            personal loans.')
            ->line('Your One Time Password (OTP) is ' . $this->otp)
            ->action('Verify Your Email', url('/api/verify-email/' . $this->email . '/' . $this->uniq_id));
    }

    # Saving data to the database
    #[ArrayShape(['msg' => "string"])] public function toDatabase(): array
    {
        return [
            'msg' => 'Verification Email Sent. Check Inbox'
        ];
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
