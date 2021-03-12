<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanPreference;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SpecialSeeder::class);
        User::factory(100)
            ->has(LoanPreference::factory()->count(1), 'loan_preference')
            ->has(Loan::factory()->count(1))
            ->has(Transaction::factory()->count(mt_rand(2, 4))
                ->has(TransactionDetail::factory()->count(1), 'transaction_detail'))
            ->create();
    }
}
