<?php

namespace App\Notifications;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ArrayShape;

class PenaltyNotification extends Notification implements ShouldQueue
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
        $url = config('app.frontEndUrl');
        return (new MailMessage)
            ->subject("Penalty Alert")
            ->greeting('Hello ' . $notifiable->name)
            ->line('You just got a penalty for an installment')
            ->line('Your penalty amount is ' . $this->installment->penalty_amount)
            ->action('Check Details', url($url . '/loans/' . $this->installment->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Saving data to the database
     * @return string[]
     */
    #[ArrayShape(['msg' => "string"])] public function toDatabase(): array
    {
        return [
            'msg' => 'Your have an installment penalty of ' . $this->installment->penalty_amount,
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
