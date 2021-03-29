<?php


namespace App\Library\Util;


class UtilFunctions
{
    # Generating a unique random array
    public function generateAnUniqueRandomNumberArray($size = 10): array
    {
        $random_number_array = range(1, 100);
        shuffle($random_number_array);
        $random_number_array = array_slice($random_number_array, 0, $size);

//        info('Following Random Array has been Generated');
//        info($random_number_array);

        return $random_number_array;
    }


}
