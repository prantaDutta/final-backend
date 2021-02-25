<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
     */
    public function definition()
    {
        $rand = mt_rand(0, 2);
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
            'mobile_no' => 8801 . mt_rand(311111111, 999999999),
            'password' => Hash::make('12345678'),
            'role' => mt_rand(0, 1) === 0 ? 'lender' : 'borrower',
            'balance' => 0.00,
            'verified' => $verified
        ];
    }
}
