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
            'verification_photos' => '{"salarySlip": "upload_15b3fd02ee5aeec3b984ad66410628c2.png", "recentPhoto": "upload_392b7e08f32064d419f808684b7a4680.png", "addressProof": "upload_a6301c09c628e5648f8627abdc33d4cd.png", "nidOrPassport": "upload_3372b237e397bbc109b8cc264948fa0a.jpg", "employeeIdCard": "upload_7a23d62d88a8999177a8f460af63ab1e.png", "bankAccountStatements": "upload_907cb6f0e5418b267309231fc0b35672.jpg#upload_4ca1b4b40ce2b8fc540c9cba8747416b.jpg#upload_217692232c84340eb094390ff41aa6ab.jpg#"}',
        ];
    }
}
