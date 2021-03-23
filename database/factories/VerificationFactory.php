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
            'verification_photos' => '{"recentPhoto": "upload_44eda4489b5ceab3cf879117c19785a5.jpg", "addressProof": "upload_6463f29a4f564fd48330f235025919d1.jpg", "nidOrPassport": "upload_4da218498b851729d184d2256eea1ca6.jpg", "bankAccountStatements": "upload_d0b598be4d344630a300fcf09d8c77cb.jpg#upload_f68d7ca36095339e620b3249ab479bec.jpg#upload_e3aba93cb22fb7885ca15572ce88c3e8.jpg#"}',
        ];
    }
}
