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

    // adds every maximum distributed amount until its equal to amount
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
     * Initializes the loan distributor
     * @param User $the_borrower
     * @param int $amount
     * @param string $unique_loan_id
     */
    public function __construct(
        protected User $the_borrower,
        protected int $amount,
        // to find the loan to distribute
        protected string $unique_loan_id,
    )
    {
    }

    /**
     * This function is called to start the distribution process
     *
     * @return void
     */
    public function distribute(): void
    {
        # Checks whether it's distributable
//        if ($this->isDistributable() === false) {
//            $this->distributeALesserAmount();
//            return;
//        }

        # Using a while loop to iterate until the loan amount is not
        # equal to distributing amount and flag is true
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
            $this->lender_ids = [];
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
            'loan_mode' => 'ongoing',
        ]);

        // attaching the loan to the borrower
        $this->the_borrower->loans()->attach($current_loan, ['amount' => $this->amount]);

        foreach ($this->lender_data as $lender) {

            $this->incrementLoanLimit($lender->lender_id);

            $user = User::find($lender->lender_id);

            # Attaching the loan to the lenders
            $user->loans()->attach($current_loan, [
                'amount' => $lender->amount,
            ]);

            $this->decrementLenderBalance($lender->lender_id, $lender->amount);

            # Creating [loan_duration] installments for the lender
            $lender_installment_amount = $lender->amount / $current_loan->loan_duration;

            $this->createInstallmentForOneUser(
                $user,
                $current_loan,
                $lender_installment_amount,
            );
        }

//        $the_borrower = $current_loan->users()
//            ->where('role', 'borrower')
//            ->first();

        $this->incrementBorrowerBalance($this->the_borrower->id, $this->amount);

        # Creating [loan_duration] installments for the borrower
        $borrower_installment_amount = $current_loan->monthly_installment_with_company_fees;

        $this->createInstallmentForOneUser(
            $this->the_borrower,
            $current_loan,
            $borrower_installment_amount,
        );
//        return response()->json([
//            'amount' => $this->amount,
//            'distributing_amount' => $this->distributing_amount,
//            'lender_data' => $this->lender_data
//        ]);
    }

    /**
     * This is where the magic happens
     * This function is responsible for distributing a loan
     * @return void
     */
    protected function distributeToALender(): void
    {
        [$user_id, $current_distributed_amount] = $this->calculateDistribution();
        if ($this->flag === true) {
            return;
        }
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
     * @return array|JsonResponse|null
     */
    protected function calculateDistribution(): array|JsonResponse|null
    {
        $user = $this->generateARandomUser();
        if ($user === null) {
            $this->handleNotFound();
            return null;
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
                    $q->whereRaw('loan_limit= (select min(`loan_limit`) from utils)');
                })
                ->where('verified', 'verified')
                ->first();

            if ($user === null) {
                $this->flag = true;
                $this->handleNotFound();
                break;
            }

        } while ($user?->balance <= $user?->loan_preference->maximum_distributed_amount);

        return $user;
    }

    /**
     * If laravel can't find a user , this function gets called
     * @return JsonResponse
     */
    protected function handleNotFound(): JsonResponse
    {
        $current_loan = Loan::where('unique_loan_id', $this->unique_loan_id)
            ->first();

        $this->the_borrower->loans()
            ->attach($current_loan, ['amount' => $this->amount]);

        $current_loan->update([
            'loan_mode' => 'failed',
        ]);

        return response()->json([
            'error' => 'Null User Found',
        ], 500);
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

    protected function decrementLenderBalance($id, $amount): void
    {
        DB::table('users')
            ->where('id', $id)
            ->decrement('balance', $amount);
    }

    protected function createInstallmentForOneUser($user, $current_loan, $installment_amount): void
    {
        for ($i = 1; $i <= $current_loan->loan_duration; $i++) {
            $due_date = today()->addMonths($i);
            $user->installments()->create([
                'amount' => $installment_amount,
                'status' => 'unpaid',
                'unique_installment_id' => uniqid('', true),
                'loan_id' => $current_loan->id,
                'penalty_amount' => 0,
                'total_amount' => $installment_amount,
                'due_date' => $due_date,
                'installment_no' => $i,
            ]);
        }
    }

    protected function incrementBorrowerBalance($id, $amount): void
    {
        DB::table('users')
            ->where('id', $id)
            ->increment('balance', $amount);
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
     * @return void
     */
    protected function distributeALesserAmount(): void
    {
        info('Inside the Lesser Amount function');
        $amount = $this->amount;

        $loan_preference = LoanPreference::where('maximum_distributed_amount', $amount)
            ->first();

        if ($loan_preference === null) {
            $this->handleNotFound();
        }

        $user = User::has('transactions')
            ->inRandomOrder()
            ->where('role', 'lender')
            ->where('balance', '>=', $amount)
            ->whereHas('util', function ($q) {
                $q->whereRaw('loan_limit= (select min(`loan_limit`) from utils)');
            })
            ->where('verified', 'verified')
            ->first();

        if ($user === null) {
            $this->handleNotFound();
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

        // attaching the loan to the borrower
        $this->the_borrower->loans()->attach($current_loan, ['amount' => $this->amount]);

        # attaching the loan to the lender
        $user->loans()->attach($current_loan, ['amount' => $this->amount]);

        $this->incrementLoanLimit($user->id);

        $this->decrementLenderBalance($user->id, $amount);

        $lender_installment_amount = $amount / $current_loan->loan_duration;

        $this->createInstallmentForOneUser(
            $user,
            $current_loan,
            $lender_installment_amount,
        );

//        $the_borrower = $current_loan->users()
//            ->where('role', 'borrower')
//            ->first();

        $this->incrementBorrowerBalance($this->the_borrower->id, $amount);

        $current_loan->update([
            'lender_data' => new LenderData(
                $user->id,
                $this->amount,
            ),
        ]);

        $borrower_installment_amount = $current_loan->monthly_installment_with_company_fees;

        $this->createInstallmentForOneUser(
            $this->the_borrower,
            $current_loan,
            $borrower_installment_amount,
        );
    }
}
