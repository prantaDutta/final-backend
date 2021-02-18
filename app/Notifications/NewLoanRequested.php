<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLoanRequested extends Notification
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
        $user = User::where('email', $notifiable->email)->first();
        $loans = $user->loans()->latest()->first();
        return (new MailMessage)
            ->subject('New Loan Requested')
            ->greeting('Hello ' . $notifiable->name)
            ->line('GrayScale is one of the fastest growing peer to peer (P2P) lending
            platforms in Bangladesh. It connects investors or lenders looking
            for high returns with creditworthy borrowers looking for short term
            personal loans.')
            ->line('We Received new Loan Request For You with following details')
            ->line('The Loan Amount: ' . $loans->loan_amount . 'Tk.')
            ->line('The Loan Duration: ' . $loans->loan_duration . 'Months')
            ->line('The Interest Rate: ' . $loans->interest_rate . '%')
            ->line('You have to pay ' . $loans->monthly_installment_with_company_fees . 'Tk. every Months')
            ->action('Go to Loans', url(config('app.frontEndUrl')) . '/loans')
            ->line('Click the button whether your loan is approved');
    }

    # Saving data to the database
    public function toDatabase($notifiable)
    {
        return [
            'msg' => 'You Requested for a new Loan'
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
