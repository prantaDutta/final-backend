<?php

namespace Database\Factories;

use App\Models\LoanPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanPreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoanPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
       $amounts = [500, 1000, 1500, 2000, 2500, 3000];
        return [
            'maximum_distributed_amount' => $amounts[mt_rand(0, count($amounts) - 1)],
        ];
    }
}
