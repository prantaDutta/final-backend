<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanPreference;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\Util;
use App\Models\Verification;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $this->call(SpecialSeeder::class);
        User::factory(1000)
            ->has(Verification::factory())
            ->has(LoanPreference::factory(), 'loan_preference')
//            ->has(Loan::factory()->count(random_int(2, 5)))
            ->hasAttached(Loan::factory()->count(random_int(2, 5)), ['amount' => 500])
            ->has(Transaction::factory()->count(random_int(2, 5))
                ->has(TransactionDetail::factory()->count(1), 'transaction_detail'))
            ->has(Util::factory())
            ->create();
    }
}
