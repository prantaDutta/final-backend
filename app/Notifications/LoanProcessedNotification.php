<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class LoanProcessedNotification extends Notification implements ShouldQueue
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
        $loan = $notifiable->loans()->latest()->first();

        $msg = 'Failed';
        if ($loan->loan_mode === 'ongoing') {
            $msg = "Successful";
        }

        if ($notifiable->role === 'lender') {
            return (new MailMessage)
                ->subject("New Loan Alert !!!!")
                ->greeting('Hello ' . $notifiable->name)
                ->line('You just got a new loan')
                ->action('Check Details', url($url . '/loans/' . $loan->id))
                ->line('Thank you for using our application!');
        }
        return (new MailMessage)
            ->subject("Loan Request " . $msg)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your new loan request was ' . $msg)
            ->action('Check Details', url($url . '/loans/' . $loan->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Saving data to the database
     * @param $notifiable
     * @return string[]
     */
    #[ArrayShape(['msg' => "string"])] public function toDatabase($notifiable): array
    {
        $loan = $notifiable->loans()->latest()->first();

        $msg = 'Failed';
        if ($loan->loan_mode === 'ongoing') {
            $msg = "Successful";
        }
        if ($notifiable->role === 'lender') {
            return [
                'msg' => 'You just got a new loan'
            ];
        }
        return [
            'msg' => 'Your loan request was ' . $msg,
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
