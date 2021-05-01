<?php

namespace Database\Factories;

use App\Http\Controllers\UtilController;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;
    protected mixed $util;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);
        $this->util = new UtilController();
    }

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        return [
//            'user_id' => mt_rand(1, 100),
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => 8801 . random_int(311111111, 999999999),
            'amount' => random_int(1000, 9999),
            'status' => random_int(0, 2) === 0 ? "Pending" : "Completed",
            'address' => $this->faker->address,
            'transaction_id' => $this->util->generateAUniqueTrxId(),
            'transaction_type' => random_int(0, 1) === 0 ? "deposit" : "withdraw",
            'currency' => "BDT",
        ];
    }
}
