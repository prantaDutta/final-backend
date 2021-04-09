<?php

namespace Database\Seeders;

use App\Library\LoanDistribution\GenerateLenderDataArray;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SpecialSeeder extends Seeder
{
    private Generator $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return Generator
     * @throws BindingResolutionException
     */
    protected function withFaker(): Generator
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $decider = random_int(0, 2);
        if ($decider === 0) {
            $mode = 'processing';
        } else if ($decider === 1) {
            $mode = 'ongoing';
        } else {
            $mode = 'finished';
        }
        $loan_amount = random_int(1000, 9999);
        $loan_duration = random_int(1, 18);
        $interest_rate = random_int(5, 15);
        $interest = $loan_amount * ($interest_rate / 100);
        $company_fees = $loan_amount * 0.02;
        $loan_start_date = Carbon::today()->subDays(random_int(0, 365));

        $users = [
            [
                'name' => 'ADMIN',
                'email' => 'admin@grayscale.com',
                'mobile_no' => '8801851944587',
                'email_verified_at' => Carbon::now(),
                'mobile_no_verified_at' => Carbon::now(),
                'role' => 'admin',
                'verified' => 'verified',
                'balance' => 0.00,
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'PRANTA Dutta',
                'email' => 'pranta@gmail.com',
                'mobile_no' => '8801851944588',
                'email_verified_at' => Carbon::now(),
                'mobile_no_verified_at' => Carbon::now(),
                'role' => 'lender',
                'verified' => 'verified',
                'balance' => 0.00,
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'Priosa Barua',
                'email' => 'priosa@gmail.com',
                'mobile_no' => '8801851944589',
                'email_verified_at' => now(),
                'mobile_no_verified_at' => now(),
                'role' => 'borrower',
                'verified' => 'verified',
                'balance' => 0.00,
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'Shifatun Tajree',
                'email' => 'tajree@gmail.com',
                'mobile_no' => '8801851944590',
                'email_verified_at' => now(),
                'mobile_no_verified_at' => now(),
                'role' => 'lender',
                'verified' => 'pending',
                'balance' => 0.00,
                'password' => Hash::make('12345678'),
            ],
        ];

        $generate_lender_array = new GenerateLenderDataArray();

        foreach ($users as $user) {
            $createdUser = User::create($user);
            for ($i = 0; $i < 5; $i++) {
                if ($createdUser->id === 1
                    || $createdUser->id === 2
                    /* || $createdUser->id === 3 */) {
                    continue;
                }
                $created_loan = Loan::create([
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
//                    'loan_start_date' => Carbon::today()->subDays(random_int(0, 365)),
//                    'loan_end_date' => Carbon::today()->addMonths(random_int(3, 18)),
                ]);

                $createdUser->loans()->attach($created_loan, ['amount' => 500]);

                $createdUser->transactions()->create([
                    'name' => $this->faker->name,
                    'email' => $this->faker->email,
                    'phone' => 8801 . random_int(311111111, 999999999),
                    'amount' => random_int(1000, 9999),
                    'status' => random_int(0, 2) === 0 ? "Pending" : "Completed",
                    'address' => $this->faker->address,
                    'transaction_id' => uniqid('', true),
                    'transaction_type' => random_int(0, 1) === 0 ? "deposit" : "withdraw",
                    'currency' => "BDT",
                ]);

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
                $loan_ids = Loan::where('id', '>', 0)
                    ->pluck('id');
                $len = count($loan_ids);
                if ($len > 1) {
                    $len = 1;
                }
//                $pos = array_search($createdUser->loans->id, (array)$loan_ids, true);
//                unset($loan_ids[$pos]);
                $penalty = $rand === 0 ? 0 : $penalty_arr[random_int(0, 3)];

                $due_date = $rand === 0
                    ? now()->subMonths(random_int(0, 5))
                    : now()->addMonths(random_int(0, 5));

                $createdUser->installments()->create([
                    'amount' => 500,
                    'status' => $status,
                    'unique_installment_id' => uniqid('', true),
                    'loan_id' => $loan_ids[random_int(1, $len)] ?? 1,
                    'penalty_amount' => $penalty,
                    'total_amount' => 500 + $penalty,
                    'due_date' => $due_date,
                    'installment_no' => random_int(1, 5),
                ]);
            }
            $rand = random_int(0, 1);
            $createdUser->verification()->create([
                'date_of_birth' => Carbon::now()->subYears(random_int(18, 26))->format('Y-m-d'),
                'gender' => $rand === 0 ? 'male' : 'female',
                'address' => $this->faker->address,
                'borrower_type' => $rand === 0 ? 'salaried' : 'self',
                'division' => 'chattogram',
                'zila' => 'chattogram',
                'zip_code' => '4000',
                'verification_photos' => '{"salarySlip": "upload_15b3fd02ee5aeec3b984ad66410628c2.png", "recentPhoto": "upload_392b7e08f32064d419f808684b7a4680.png", "addressProof": "upload_a6301c09c628e5648f8627abdc33d4cd.png", "nidOrPassport": "upload_3372b237e397bbc109b8cc264948fa0a.jpg", "employeeIdCard": "upload_7a23d62d88a8999177a8f460af63ab1e.png", "bankAccountStatements": "upload_907cb6f0e5418b267309231fc0b35672.jpg#upload_4ca1b4b40ce2b8fc540c9cba8747416b.jpg#upload_217692232c84340eb094390ff41aa6ab.jpg#"}',
            ]);
            $amounts = [500, 1000, 1500, 2000, 2500, 3000];

            $createdUser->loan_preference()->create([
                'maximum_distributed_amount' => $amounts[random_int(0, count($amounts) - 1)],
            ]);

            $createdUser->util()->create([
                'loan_limit' => 0,
            ]);
        }

        $admin = User::find(1);
        $admin->administration()->create([
            'penalty_data' => config('constants.penalty_data'),
            'default_interest_rate' => config('constants.default_interest_rate'),
        ]);
    }
}
