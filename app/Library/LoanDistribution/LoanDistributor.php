<?php

namespace App\Library\LoanDistribution;

use App\Models\Loan;
use App\Models\LoanPreference;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LoanDistributor implements ShouldQueue
{
    use Queueable;

    // adds every maximum distributed amount
    protected int $distributing_amount = 0;

    // when true, the loan will be distributed
    protected bool $flag = false;

    // contains lender_id and amount
    protected array $lender_data = [];

    // contains lender_ids, for performance boost
    protected array $lender_ids = [];

    // to restart the distribution
    protected int $retry_count = 0;

    /**
     * LoanDistributor constructor.
     * Initializes the distribution amount
     * @param int $amount
     * @param string $unique_loan_id
     * @return void
     */
    public function __construct(
        protected int $amount,
        // to find the loan to distribute
        protected string $unique_loan_id,
    )
    {
    }

    /**
     * This function is called to start the distribution process
     * It uses a while loop to iterate until the loan amount is not
     *  equal to distributing amount and flag is true
     * @return void
     */
    public function distribute(): void
    {
        # Checks whether it's distributable
        if ($this->isDistributable() === false) {
            $this->distributeALesserAmount();
        }

        while ($this->flag === false) {
            if ($this->amount > $this->distributing_amount) {
                $this->distributeToALender();
                continue;
            }

            if ($this->amount === $this->distributing_amount) {
                $this->flag = true;
                break;
            }
        }

        # This condition just checks whether amount is equal to original amount
        # It adds all amounts and checks with the original amount
        if ($this->checkIfLenderAmountsAreEqualToTheOriginalAmount() === false) {
            $this->distributing_amount = 0;
            $this->lender_data = [];
            $this->flag = false;
            $this->retry_count++;

            # this condition will check the retry count
            # basically it will try 3 times,
            # if failed more than 3 times, it will return error
            if ($this->retry_count >= 3) {
                $this->handleNotFound();
            }

            $this->distribute();
        }

        info("##################################################");
        info('Distribution Successful');
        info("##################################################");

        # Saving the data to the database
        $current_loan = Loan::where('unique_loan_id', $this->unique_loan_id)
            ->first();

        if (!$current_loan) {
            $this->handleNotFound();
        }

        $current_loan->update([
            'lender_data' => $this->lender_data,
        ]);

        foreach ($this->lender_data as $lender) {
            $this->incrementLoanLimit($lender->lender_id);
            DB::table('users')
                ->where('id', $lender->lender_id)
                ->decrement('balance', $lender->amount);
        }

//        return response()->json([
//            'amount' => $this->amount,
//            'distributing_amount' => $this->distributing_amount,
//            'lender_data' => $this->lender_data
//        ]);
    }

    /**
     * This function checks whether the amount is distributable or not
     * @return bool
     */
    protected function isDistributable(): bool
    {
        return $this->amount >= 2000;
    }

    /**
     * If the amount is not distributable, this function will get called
     * It will find an user with the loan amount and return it
     * @return JsonResponse
     */
    protected function distributeALesserAmount(): JsonResponse
    {
        info('Inside the Lesser Amount function');
        $amount = $this->amount;

        # Every do-while loop executes first and then checks the condition
        do {
            $loan_preference = LoanPreference::where('maximum_distributed_amount', $amount)->first();

            if ($loan_preference === null) {
                $this->handleNotFound();
            }

            $user = User::findOrFail($loan_preference->user_id);
        } while ($user->loan_preference->loan_limit > 5);

        $this->incrementLoanLimit($user->id);
        return response()->json([
            'lender_data' => new LenderData(
                $user->id,
                $this->amount,
            ),
        ]);
    }

    /**
     * If laravel can't find a user , this function gets called
     * @return JsonResponse
     */
    protected function handleNotFound(): JsonResponse
    {
        return response()->json([
            'error' => 'Null User Found',
        ], 500);
    }

    /**
     * incrementing loan limit
     * @param $id
     * @return int
     */
    protected function incrementLoanLimit($id): int
    {
        return DB::table('utils')
            ->where('user_id', $id)
            ->increment('loan_limit');
    }

    /**
     * This is where the magic happens
     * This function is responsible for distributing a loan
     * @return void
     */
    protected function distributeToALender(): void
    {
        [$user_id, $current_distributed_amount] = $this->calculateDistribution();
        $this->distributing_amount += $current_distributed_amount;

        # If distributing amount gets bigger than loan amount
        # Then we are subtracting the current distributing amount
        # Then finding the difference between loan amount and distributing amount
        # And assigning it to current distributing amount
        # So the amount and the distributing amount stays the same

        if ($this->distributing_amount > $this->amount) {
            $this->flag = true;
            $this->distributing_amount -= $current_distributed_amount;
            $current_distributed_amount = $this->amount - $this->distributing_amount;
            $this->distributing_amount += $current_distributed_amount;
        }

        # assigning user id to lender id array
        # so that we do not include those id when
        # generating a random user
        $this->lender_ids[] = $user_id;

        $this->lender_data[] = new LenderData(
            $user_id,
            $current_distributed_amount,
        );
    }

    /**
     * This function calculates the necessary condition for loan distribution
     * @return array|JsonResponse
     */
    protected function calculateDistribution(): array|JsonResponse
    {
        $user = $this->generateARandomUser();
        if ($user === null) {
            $this->handleNotFound();
        }

        $maximum_distributed_amount = $user->loan_preference->maximum_distributed_amount;
        $divisor = $maximum_distributed_amount / 500;

        # This generates a random number to slice a multiplier of 500
        # From the maximum distributed amount
        try {
            $random_number = random_int(1, $divisor / 2 < 1 ? 1 : $divisor / 2);
        } catch (Exception) {
            info('Error while generating random number');
            return $this->handleNotFound();
        }

        $current_distributed_amount = 500 * $random_number;
//        $this->incrementLoanLimit($user->id);
        return array($user->id, $current_distributed_amount);
    }

    /**
     * Just generates a random user with some conditions
     * @return \Illuminate\Database\Eloquent\Builder|User|Builder|null
     */
    protected function generateARandomUser(): \Illuminate\Database\Eloquent\Builder|User|Builder|null
    {
        do {
            $user = User::has('transactions')
                ->inRandomOrder()
                ->where('role', 'lender')
                ->whereNotIn('id', $this->lender_ids)
                ->whereHas('util', function ($q) {
                    $q->where('loan_limit', '<=', 5);
                })
                ->where('verified', 'verified')
                ->first();

            if ($user === null) {
                $this->handleNotFound();
            }

        } while ($user->balance < $user->loan_preference->maximum_distributed_amount);

        return $user;
    }

    protected function checkIfLenderAmountsAreEqualToTheOriginalAmount(): bool
    {
        $total_amount = 0;
        if (count($this->lender_data) > 1) {
            foreach ($this->lender_data as $index => $data) {
                $total_amount += $data->amount;
            }

            info('From The Tester');
            info('Original Amount: ' . $this->amount);
            info('Distributing Amount: ' . $this->distributing_amount);
            info('Total Lender\'s Amount: ' . $total_amount);

            if ($total_amount !== $this->amount) {
                return false;
            }
        }

        return true;
    }
}
