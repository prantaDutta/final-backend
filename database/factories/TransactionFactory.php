<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => mt_rand(1, 100),
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'amount' => mt_rand(1000, 9999),
            'status' => mt_rand(0, 2) === 0 ? "Pending" : "Completed",
            'address' => $this->faker->address,
            'transaction_id' => uniqid('', true),
            'transaction_type' => mt_rand(0, 1) === 0 ? "deposit" : "withdraw",
            'currency' => "BDT",
        ];
    }
}
