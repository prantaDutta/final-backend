<?php

namespace Database\Factories;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        $rand = random_int(0, 2);
        if ($rand === 0) {
            $verified = 'pending';
        } else if ($rand === 1) {
            $verified = 'unverified';
        } else {
            $verified = 'verified';
        }
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'mobile_no_verified_at' => now(),
            'mobile_no' => 8801 . random_int(311111111, 999999999),
            'password' => bcrypt('12345678'),
            'role' => random_int(0, 1) === 0 ? 'lender' : 'borrower',
            'balance' => 5000,
            'verified' => $verified
        ];
    }
}
