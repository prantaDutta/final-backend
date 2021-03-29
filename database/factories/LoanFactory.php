<?php

namespace Database\Factories;

use App\Library\LoanDistribution\GenerateLenderDataArray;
use App\Models\Loan;
use Carbon\Carbon;
use Exception;
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
     * @throws Exception
     */
    public function definition(): array
    {
        $decider = random_int(0, 3);

        if ($decider === 0) {
            $mode = 'processing';
        } else if ($decider === 1) {
            $mode = 'ongoing';
        } else if ($decider === 2) {
            $mode = 'finished';
        } else {
            $mode = 'failed';
        }

        $loan_amount = random_int(1000, 9999);
        $loan_duration = random_int(1, 18);
        $interest_rate = random_int(5, 15);
        $interest = $loan_amount * ($interest_rate / 100);
        $company_fees = $loan_amount * 0.02;
//        $loan_start_date = Carbon::today()->subDays(random_int(0, 365));

        $generate_lender_array = new GenerateLenderDataArray();

        return [
            'loan_amount' => $loan_amount,
            'lender_data' => $generate_lender_array->generate($loan_amount),
            'unique_loan_id' => uniqid('', true),
            'loan_mode' => $mode,
            'loan_duration' => $loan_duration,
            'interest_rate' => $interest_rate,
            'amount_with_interest' => $loan_amount + $interest,
            'company_fees' => $company_fees,
            'amount_with_interest_and_company_fees' => $loan_amount + $interest + $company_fees,
            'monthly_installment' => ($loan_amount + $interest) / $loan_duration,
            'monthly_installment_with_company_fees' => ($loan_amount + $interest + $company_fees) / $loan_duration,
            'loan_start_date' => Carbon::today()->subDays(random_int(0, 365)),
            'loan_end_date' => Carbon::today()->addMonths(random_int(3, 18)),
        ];
    }
}
