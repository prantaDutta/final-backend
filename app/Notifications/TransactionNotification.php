<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class TransactionNotification
 * @package App\Notifications
 */
class TransactionNotification extends Notification implements ShouldQueue
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
    public function toMail(mixed $notifiable): MailMessage
    {
        $url = config('app.frontEndUrl');
        $transaction = $notifiable->transactions()->latest()->first();
        $status = $transaction->status;
        $type = $transaction->transaction_type;
        return (new MailMessage)
            ->subject(ucfirst($type) . ' ' . $status)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your ' . ucfirst($type) . ' Request is ' . $status)
            ->action('Check Details', url($url . '/' . $type . 's' . '/' . $transaction->id));
    }

    /**
     * Saving data to the database
     * @param $notifiable
     * @return string[]
     */
    #[ArrayShape(['msg' => "string"])] public function toDatabase($notifiable): array
    {
        $transaction = $notifiable->transactions()->latest()->first();
        $status = $transaction->status;
        $type = $transaction->transaction_type;
        return [
            'msg' => 'Your ' . $type . ' was ' . $status,
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
