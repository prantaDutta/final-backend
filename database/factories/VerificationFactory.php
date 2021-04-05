<?php

namespace Database\Factories;

use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Verification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $rand = mt_rand(0,1);
        return [
            'date_of_birth' => Carbon::now()->subYears(mt_rand(18, 26))->format('Y-m-d'),
            'gender' => $rand === 0 ? 'male' : 'female',
            'address' => $this->faker->address,
            'borrower_type' => $rand === 0 ? 'salaried' : 'self',
            'division' => 'chattogram',
            'zila' => 'chattogram',
            'zip_code' => '4000',
            'verification_photos' => '{"recentPhoto": "upload_f09dc1ea9938e0f078bf34500df6645a.jpg", "addressProof": "upload_eab542cb683d754026f125db68870ca3.png", "businessProof": "upload_3eadf6690c04522340172b15efbc9e73.png", "nidOrPassport": "upload_a65039e0876521f5eb1f1b5036478e4c.jpg", "bankAccountStatements": "upload_b35667317d98ff06951979ce55014a68.jpg#upload_976a3309f876ebbe87b61e3f5fad7f16.jpg#upload_d8856687bc2b2256811dc42bded3153a.jpg#"}',
        ];
    }
}
