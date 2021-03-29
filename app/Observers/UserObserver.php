<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\SendVerifyEmailOTP;
use App\Notifications\WelcomeMessage;
use Illuminate\Bus\Queueable;

class UserObserver
{
    use Queueable;
    /**
     * Handle the User "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
//        $uniq_id = uniqid('', true);
//        $otp = mt_rand(100000, 999999);
//        $user->util()->create([
//            'email_verify_token' => $uniq_id,
//            'email_verify_otp' => $otp,
//            'loan_limit' => 0,
//        ]);
//        $user->notify(new WelcomeMessage());
//        $user->notify(new SendVerifyEmailOTP($user->name, $user->email, $otp, $uniq_id));
//
//        $user->loan_preference()->create([
//            'distributed_amounts' => '500, 1000',
//        ]);
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
