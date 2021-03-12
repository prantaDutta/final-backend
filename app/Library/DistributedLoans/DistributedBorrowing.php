<?php

namespace App\Library\DistributedLoans;

use App\Models\User;
use Illuminate\Bus\Queueable;

class DistributedBorrowing
{
    use Queueable;

    private $amount;
    private $distributing_amount;
    private $flag;

    /**
     * DistributedBorrowing constructor
     * Initialized the class with the distributing amount
     * @param $amount
     */
    public function __construct($amount)
    {
        $this->amount = $amount;
        $this->distributing_amount = 0;
        $this->flag = false;
    }

    public function distribute()
    {
        $amount = $this->amount;
        if ($this->isDistributable()) {
            $divisors = $amount / 500;
            $random_users = User::inRandomOrder()->where('role', 'lender')->limit($divisors)->get();
            foreach ($random_users as $user) {
                if ($this->flag) {
                    break;
                }
                $this->distributeToAnUser($user);
            }
        }
        return 'distributing amount: ' . $this->distributing_amount . ' amount: ' . $this->amount;
    }

    /**
     * Checks whether we can distribute the amount
     * @return bool
     */
    protected function isDistributable()
    {
        return $this->amount >= config('app.minimum_distributed_amount');
    }

    protected function distributeToAnUser(User $user)
    {
        if (!isset($user->loan_preference->distributed_amounts)) {
            return null;
        }
        $distributed_array = explode(", ", $user->loan_preference->distributed_amounts);
        $loan_amount = $distributed_array[mt_rand(0, count($distributed_array) - 1)];

//        if ($this->distributing_amount === $this->amount){
//            return $this->flag = true;
//        } else {
//            return $this->distributing_amount += $loan_amount;
//        }
//
//        if ($this->distributing_amount > $this->amount) {
//            $this->flag = true;
//            $temp = $this->distributing_amount - $this->amount;
//            return $this->distributing_amount += $temp;
//        }

        return $this->distributing_amount += $loan_amount;
    }

    protected function checkDistributedAmount() {
        if ($this->amount === $this->distributing_amount){
            return $this->flag = true;
        }

        if ($this->distributing_amount > $this->amount) {
            $this->flag = true;

        }
    }
}
