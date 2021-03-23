<?php


namespace App\Library\LoanDistribution;


use App\Models\User;
use Exception;

class GenerateLenderDataArray
{
    public function generate($amount): array
    {
        $random_amount_array = [500, 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000];

        $temp = 0;
        $lender_ids = [];
        $lender_data = [];

        $random_number_array = [25, 45, 55, 44, 462, 225];
        while ($amount !== $temp) {
            try {
                $rand = $random_amount_array[random_int(0, 9)];
            } catch (Exception) {
                $rand = 500;
            }

            $temp += $rand;

            if ($amount < $temp) {
                $temp -= $rand;
                $rand = $amount - $temp;
                $temp += $rand;
            }

            try {

                $random_user_id = User::all()
                    ->where('role', 'lender')
                    ->where('verified', 'verified')
                    ->whereNotIn('id', $lender_ids)
                    ->random()->id;
            } catch (Exception) {
                $random_user_id = $random_number_array[random_int(0, 4)];
            }

            $lender_ids[] = $random_user_id;

            $lender_data[] = new LenderData(
                $random_user_id,
                $rand,
            );
        }

        return $lender_data;
    }
}
