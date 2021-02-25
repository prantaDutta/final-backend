<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SpecialSeeder extends Seeder
{
    private $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
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
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
                'verified' => 'unverified',
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
        foreach ($users as $user) {
            $createdUser = User::create($user);
            for ($i = 0; $i < 5; $i++) {
                $createdUser->loans()->create([
                    'loan_amount' => $loan_amount,
                    'loan_mode' => $mode,
                    'loan_duration' => $loan_duration,
                    'interest_rate' => $interest_rate,
                    'amount_with_interest' => $loan_amount + $interest,
                    'company_fees' => $company_fees,
                    'amount_with_interest_and_company_fees' => $loan_amount + $interest + $company_fees,
                    'monthly_installment' => ($loan_amount + $interest) / $loan_duration,
                    'monthly_installment_with_company_fees' => ($loan_amount + $interest + $company_fees) / $loan_duration,
//                    'loan_start_date' => $loan_start_date,
//                    'loan_end_date' => $loan_start_date->addMonths($loan_duration),
                ]);
                $createdUser->transactions()->create([
                    'name' => $this->faker->name,
                    'email' => $this->faker->email,
                    'phone' => 8801 . mt_rand(311111111, 999999999),
                    'amount' => mt_rand(1000, 9999),
                    'status' => mt_rand(0, 2) === 0 ? "Pending" : "Completed",
                    'address' => $this->faker->address,
                    'transaction_id' => uniqid('', true),
                    'transaction_type' => mt_rand(0, 1) === 0 ? "deposit" : "withdraw",
                    'currency' => "BDT",
                ]);
            }
        }
    }

}
