<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\LoanUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        LoanUser::create([
            'user_id' => mt_rand(1,100),
            'loan_id' => mt_rand(1,100),
        ]);

        $decider = mt_rand(0, 2);
        if ($decider === 0) {
            $mode = 'processing';
        } else if ($decider === 1) {
            $mode = 'ongoing';
        } else {
            $mode = 'finished';
        }
        $loan_amount = mt_rand(1000, 9999);
        $loan_duration = mt_rand(1, 18);
        $interest_rate = mt_rand(5, 15);
        $interest = $loan_amount * ($interest_rate / 100);
        $company_fees = $loan_amount * 0.02;
        $loan_start_date = Carbon::today()->subDays(mt_rand(0, 365));
        return [
            'loan_amount' => $loan_amount,
            'loan_mode' => $mode,
            'loan_duration' => $loan_duration,
            'interest_rate' => $interest_rate,
            'amount_with_interest' => $loan_amount + $interest,
            'company_fees' => $company_fees,
            'amount_with_interest_and_company_fees' => $loan_amount + $interest + $company_fees,
            'monthly_installment' => ($loan_amount + $interest) / $loan_duration,
            'monthly_installment_with_company_fees' => ($loan_amount + $interest + $company_fees) / $loan_duration,
            'loan_start_date' => $loan_start_date,
            'loan_end_date' => $loan_start_date->addMonths($loan_duration),
        ];
    }
}
