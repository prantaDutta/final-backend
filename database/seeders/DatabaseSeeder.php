<?php

namespace Database\Seeders;

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
//         User::factory(100)
//             ->has(Loan::factory()->count(5))
//             ->has(Transaction::factory()->count(5)
//                 ->has(TransactionDetail::factory()->count(1), 'transaction_detail'))
//             ->create();

    }
}
