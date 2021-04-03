<?php

namespace Database\Factories;

use App\Models\Installment;
use App\Models\Loan;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class  InstallmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Installment::class;

    /**
     * Define the model's default state.
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        $rand = random_int(0, 2);
        $penalty_arr = [10, 20, 30, 40];
        $status = 'unpaid';
        if ($rand === 0) {
            $status = 'paid';
        }
        if ($rand === 1) {
            $status = 'due';
        }
        # This will give us all the ids of loan table
        $loan_ids = Loan::where('id', '>', 0)->pluck('id');
//                $pos = array_search($createdUser->loans->id, (array)$loan_ids, true);
//                unset($loan_ids[$pos]);
        $penalty = $rand === 0 ? 0 : $penalty_arr[random_int(0, 3)];

        $due_date = $rand === 0
            ? now()->subMonths(random_int(0, 5))
            : now()->addMonths(random_int(0, 5));
        return [
            'amount' => 500,
            'status' => $status,
            'unique_installment_id' => uniqid('', true),
            'loan_id' => $loan_ids[random_int(0, count($loan_ids))] ?? 2,
            'penalty_amount' => $penalty,
            'total_amount' => 500 + $penalty,
            'due_date' => $due_date,
            'installment_no' => random_int(1, 5),
        ];
    }
}
