<?php

namespace Database\Seeders;

use App\Models\Loan;
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
         User::factory(100)->count(5)->create();
         Loan::factory(200)->create();
         Transaction::factory(200)->create();
         TransactionDetail::factory(200)->create();
    }
}
