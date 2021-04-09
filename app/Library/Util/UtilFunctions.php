<?php


namespace App\Library\Util;


use Illuminate\Http\JsonResponse;

class UtilFunctions
{
    # Generating a unique random array
    public function generateAnUniqueRandomNumberArray($size = 10): array
    {
        $random_number_array = range(1, 100);
        shuffle($random_number_array);
        $random_number_array = array_slice($random_number_array, 0, $size);

        return $random_number_array;
    }

    // checking authorization
    // if the data actually belongs to the user
    public function checkAuthorization($user, $something): bool
    {
        $flag = true;
        foreach ($something->users as $an_user) {
            if ($an_user->id !== $user->id) {
                info('user_id: ' . $user->id);
                info('an user id: ' . $an_user->id);
                $flag = false;
            }
        }

        return $flag;
    }

    // Return Error
    protected function error($code = 500, $msg = "ERROR"): JsonResponse
    {
        return response()->json([$msg], $code);
    }
}
