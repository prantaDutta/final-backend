<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$/huF2ZU4xHaZfzUgFjJQn.dPAxVXhTIWx2ccuXH4ySDRwdaFW4Wie', // 12345678
            'role' => mt_rand(0,1)=== 0 ? 'lender' : 'borrower',
            'verified' => 'pending'
        ];
    }
}
