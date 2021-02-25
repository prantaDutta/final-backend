<?php

namespace Database\Factories;

use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TransactionDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $random_num = mt_rand(0,3);
        if ($random_num === 0) {
            $card_type = "BKASH-BKash";
            $card_no = '450850******4050';
            $card_issuer = "BKash Mobile Banking";
            $card_brand = "MOBILE BANKING";
        } else if ($random_num === 1) {
            $card_type = "ROCKET-Rocket";
            $card_no = '432155******3964';
            $card_issuer = "Rocket Mobile Banking";
            $card_brand = "MOBILE BANKING";
        } else if ($random_num === 2) {
            $card_type = "NOGOD-Nogod";
            $card_no = "455445XXXXXX4326";
            $card_issuer = "Nogod Mobile Banking";
            $card_brand = "MOBILE BANKING";
        } else {
            $card_type = "VISA-Dutch Bangla";
            $card_no = "455445XXXXXX4326";
            $card_issuer = "STANDARD CHARTERED BANK";
            $card_brand = "VISA";
        }
        return [
//            'transaction_id' => mt_rand(1, 100),
            'card_type' => $card_type,
            'card_no' => $card_no,
            'bank_tran_id' => uniqid('',true),
            'error' => "N/A",
            'card_issuer' => $card_issuer,
            'card_brand' => $card_brand,
            'risk_level' => 0,
            'risk_title' => 'safe'
        ];
    }
}
