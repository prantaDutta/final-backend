<?php

use App\Library\Util\PenaltyData;

$penalty_data = [];
$penalty = 0;

for ($i = 1; $i <= 31; $i++) {
    if ($i >= 10) {
        $penalty = 20;
    }
    if ($i >= 15) {
        $penalty = 40;
    }
    for ($j = 16; $j <= $i; $j++) {
        $penalty += 10;
    }
    $penalty_data[] = new PenaltyData($i, $penalty);
}

return [
    'penalty_data' => $penalty_data,
    'default_interest_rate' => env('DEFAULT_INTEREST_RATE', 7),
];
