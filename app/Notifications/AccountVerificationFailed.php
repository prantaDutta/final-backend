<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class AccountVerificationFailed extends Notification
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
     * @return array
     */
    public function via(): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verification Denied')
            ->greeting('Hello ' . $notifiable->name)
            ->line('GrayScale is one of the fastest growing peer to peer (P2P) lending
            platforms in Bangladesh. It connects investors or lenders looking
            for high returns with creditworthy borrowers looking for short term
            personal loans.')
            ->line('Thank you for your verification request. We reviewed your documents
            and found one or more documents or information to be unauthentic')
            ->line('Please Submit Valid papers and information')
            ->action('Let\'s Start', url(config('app.frontEndUrl')))
            ->line('Verify Your Account and Start Today');
    }

    #[ArrayShape(['msg' => "string"])] public function toDatabase(): array
    {
        return [
            'msg' => 'Your Account Verification is Denied',
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
