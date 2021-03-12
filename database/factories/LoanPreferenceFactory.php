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
        $numbers = [500, 1000];
        $random = mt_rand(1, 5);
        for ($i = 3; $i <= $random; $i++) {
            $numbers[$i] = 500 * $i;
        }
        return [
            'distributed_amounts' => implode(', ', $numbers),
        ];
    }
}
