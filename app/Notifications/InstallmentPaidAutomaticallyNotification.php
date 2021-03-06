<?php

namespace App\Notifications;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class InstallmentPaidAutomaticallyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Installment $installment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public int $installment_id
    )
    {
        $this->installment = Installment::findOrFail($this->installment_id);
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
        $installment = $this->installment;

        return (new MailMessage)
            ->subject("Installment Paid Automatically")
            ->greeting('Hello ' . $notifiable->name)
            ->line("Our system just paid one of your installment with " . $installment->total_amount . 'Tk. automatically')
            ->action('Check Details', url($url . '/installments/' . $installment->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Sending Notifications to the user
     * @param $notifiable
     * @return string[]
     */
    #[ArrayShape(['msg' => "string"])] public function toDatabase($notifiable): array
    {
        return [
            'msg' => "Your installment got paid automatically",
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
