<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\SentVerifyEmailOTPNotification;
use App\Notifications\WelcomeMessageNotification;
use Exception;
use Illuminate\Bus\Queueable;

class UserObserver
{
    use Queueable;

    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function created(User $user): void
    {
        $uniq_id = uniqid('', true);
        $otp = random_int(100000, 999999);

        $user->util()->create([
            'email_verify_token' => $uniq_id,
            'email_verify_otp' => $otp,
            'loan_limit' => 0,
        ]);

        $user->loan_preference()->create([
            'maximum_distributed_amount' => 500,
        ]);
        $user->notify(new WelcomeMessageNotification());
        $user->notify(new SentVerifyEmailOTPNotification($user->name, $user->email, $otp, $uniq_id));
    }

    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
