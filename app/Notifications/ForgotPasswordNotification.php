<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class ForgotPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $name, $email, $uniq_id;

    /**
     * Create a new notification instance.
     *
     * @param $name
     * @param $email
     * @param $uniq_id
     */
    public function __construct($name, $email, $uniq_id)
    {
        $this->name = $name;
        $this->email = $email;
        $this->uniq_id = $uniq_id;
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
        $url = config('app.frontEndUrl');
        return (new MailMessage)
            ->subject('Forgot Password')
            ->greeting('Hello ' . $this->name)
            ->line('GrayScale is one of the fastest growing peer to peer (P2P) lending
            platforms in Bangladesh. It connects investors or lenders looking
            for high returns with creditworthy borrowers looking for short term
            personal loans.')
            ->line('You just requested to change your password')
            ->action('Change Your Password', url($url . '/change-password/?email=' . $this->email . '&token=' . $this->uniq_id));
    }

    /**
     * Sending Notifications to the user
     * @param $notifiable
     * @return string[]
     */
    #[ArrayShape(['msg' => "string"])] public function toDatabase($notifiable): array
    {
        return [
            'msg' => "Password Changed Requested",
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
